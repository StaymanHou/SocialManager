from basic_posterhandler import *
from time import sleep
import urllib
import requests
import json
from lxml import etree
from ..MyQueue import *
from ..RssPost import *
from ..Tags import *

class handler(basicposterhandler):

    def __init__(self):
        super(handler, self).__init__()
        self.module_name = 'tumblr'

    # override
    def without_session(self, load_iteration=1):
        s = requests.Session()
        # visit login page and get cookie
        url = 'https://www.tumblr.com/login'
        r = s.get(url)
        if r.status_code!=200:
            raise Exception('unexpected response: %s : %s'%(url, r.status_code))
        htmltree = etree.HTML(r.text)
        elem = htmltree.xpath('//input[@name="recaptcha_public_key"]')
        if len(elem)==0:
            raise Exception('can\'t get //input[@name="recaptcha_public_key"]')
        recaptcha_public_key = elem[0].get('value')
        elem = htmltree.xpath('//input[@name="form_key"]')
        if len(elem)==0:
            raise Exception('can\'t get //input[@name="form_key"]')
        capture = form_key = elem[0].get('value')
        elems = htmltree.xpath('//img[@style and @src]')
        for elem in elems:
            url = elem.get('src')
            sleep(load_iteration)
            r = s.get(url)
            if r.status_code!=200:
                raise Exception('unexpected response: %s : %s'%(url, r.status_code))
        addcookie = {'capture': capture}
        # login
        url = 'https://www.tumblr.com/login'
        payload = {'user[password]': self.AccSet['PSWD'],
                   'user[email]': self.AccSet['USERNAME'],
                   'user[age]': '',
                   'used_suggestion': '0',
                   'tumblelog[name]': '',
                   'seen_suggestion': '0',
                   'recaptcha_response_field': '',
                   'recaptcha_public_key': recaptcha_public_key,
                   'form_key': form_key,
                   'context': 'no_referer'}
        r = s.post(url, data=payload, cookies=addcookie)
        if r.status_code!=200:
            raise Exception('unexpected response: %s : %s'%(url, r.status_code))
        self.session = s
        sleep(load_iteration)        
        self.with_session()

    # override
    def with_session(self, load_iteration=1):
        s = self.session
        # go to dashboard
        url = 'https://www.tumblr.com/dashboard'
        r = s.get(url)
        if r.status_code!=200:
            raise Exception('unexpected response: %s : %s'%(url, r.status_code))
        # extract form key, blog_name
        htmltree = etree.HTML(r.text)
        elem = htmltree.xpath('//form[@id="search_form"]/input[@name="form_key"]')
        if len(elem)==0:
            raise Exception('can\'t get //form[@id="search_form"]/input[@name="form_key"]')
        form_key = elem[0].get('value')
        elem = htmltree.xpath('//form[@id="search_form"]/input[@name="t"]')
        if len(elem)==0:
            raise Exception('can\'t get //form[@id="search_form"]/input[@name="t"]')
        blog_name = elem[0].get('value')
        if ('blog_name' in self.AccSet['OTHER_SETTING']) and (self.AccSet['OTHER_SETTING']['blog_name'] is not None) and (self.AccSet['OTHER_SETTING']['blog_name'].strip()!=''):
            blog_name = self.AccSet['OTHER_SETTING']['blog_name'].strip()        
        if ('blog_name' in self.QI['OTHER_FIELD']) and (self.QI['OTHER_FIELD']['blog_name'] is not None) and (self.QI['OTHER_FIELD']['blog_name'].strip()!=''):
            blog_name = self.QI['OTHER_FIELD']['blog_name'].strip()
        sleep(load_iteration)
        if self.QI['TYPE']==1:
            # type 1 = link
            # get data of link
            url = 'http://www.tumblr.com/svc/post/fetch_og'
            payload = {'form_key': form_key,
                       'url': self.QI['LINK']}
            headers = {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            r = s.post(url, data=urllib.urlencode(payload), headers=headers)           
            if r.status_code!=200:
                raise Exception('tumblr post handle unexpected response: %s : %s'%(url, r.status_code))
            response_json = json.loads(r.text)
            # organize content
            thumbnail = ''
            if self.QI['IMAGE_LINK']: thumbnail = self.QI['IMAGE_LINK'].strip()
            title = self.QI['TITLE']
            content = self.QI['CONTENT']
            if self.QI['TAG'] is None: tag = ''
            else: tag = ','.join([tag.strip() for tag in self.QI['TAG'].split(',')])
            if thumbnail is None or thumbnail.strip() == '':
                if 'image' in response_json['response'] and response_json['response']['image'].strip() != '': thumbnail = response_json['response']['image']
                else: thumbnail = ''
            if title is None or title.strip() == '':
                if 'title' in response_json['response'] and response_json['response']['title'].strip() != '': title = response_json['response']['title']
                else: title = ''
            if content is None or content.strip() == '':
                if 'description' in response_json['response'] and response_json['response']['description'].strip() != '':
                    content = response_json['response']['description']
                    content = ''.join(['<p>'+par.strip()+'</p>' for par in content.split('\n')])
                else: content = ''  
            content += self.QI['EXTRA_CONTENT']
            # post
            url = 'http://www.tumblr.com/svc/post/update'
            payload = {'form_key': form_key,
                       'post': {},
                       'context_id': blog_name,
                       'context_page': 'dashboard',
                       'editor_type': 'rich',
                       'is_rich_text[one]': '0',
                       'is_rich_text[two]': '0',
                       'is_rich_text[three]': '1',
                       'channel_id': blog_name,
                       'post[slug]': '',
                       'post[source_url]': 'http://',
                       'post[date]': '',
                       'post[type]': 'link',
                       'remove_thumbnail': '',
                       'thumbnail_pre_upload': '1',
                       'thumbnail': thumbnail,
                       'post[two]': self.QI['LINK'],
                       'post[one]': title.encode('ascii', 'ignore'),#title
                       'post[three]': content.encode('ascii', 'ignore'),#content change
                       'post[tags]': tag,# change what if 2, ','.join
                       'post[publish_on]': '',
                       'post[state]': '0'}
            headers = {'Content-type': 'application/json', 'Accept': 'application/json, text/javascript, */*'}
            r = s.post(url, data=json.dumps(payload), headers=headers)
            if r.status_code!=200:
                raise Exception('unexpected response: %s : %s'%(url, r.status_code))
            # check success
            response_json = json.loads(r.text)
            if 'errors' in response_json and response_json['errors']:
                raise Exception('handle failed: %s'%r.text)
        elif self.QI['TYPE']==2 and self.QI['IMAGE_FILE']:
            # type 2 = photo
            link_anchor_text = self.QI['LINK']
            if ('link_anchor_text' in self.AccSet['OTHER_SETTING'] and self.AccSet['OTHER_SETTING']['link_anchor_text'] is not None and self.AccSet['OTHER_SETTING']['link_anchor_text'].strip()!=''):
                link_anchor_text = self.AccSet['OTHER_SETTING']['link_anchor_text'].strip()
            if ('link_anchor_text' in self.QI['OTHER_FIELD'] and self.QI['OTHER_FIELD']['link_anchor_text'] is not None and self.QI['OTHER_FIELD']['link_anchor_text'].strip()!=''):
                link_anchor_text = self.QI['OTHER_FIELD']['link_anchor_text'].strip()
            content = '<p><strong>'+self.QI['TITLE']+'</strong></p><p></p>'+self.QI['CONTENT']+'<p><em><a href="'+self.QI['LINK']+'">'+link_anchor_text+'</a></em></p>'+self.QI['EXTRA_CONTENT']
            if self.QI['TAG'] is None: tag = ''
            else: tag = ','.join([tag.strip() for tag in self.QI['TAG'].split(',')])
            url = 'http://www.tumblr.com/svc/post/update'
            payload = {'form_key': form_key,
                       'context_id': blog_name,
                       'context_page': 'dashboard',
                       'editor_type': 'rich',
                       'is_rich_text[one]': '0',
                       'is_rich_text[two]': '1',
                       'is_rich_text[three]': '0',
                       'channel_id': blog_name,
                       'post[slug]': '',
                       'post[source_url]': 'http://',
                       'post[date]': '',
                       'post[three]': self.QI['LINK'],# click through link
                       'MAX_FILE_SIZE': '10485760',
                       'post[type]': 'photo',
                       'post[two]': content.encode('ascii', 'ignore'),# content
                       'post[tags]': tag,# tag list
                       'post[publish_on]': '',
                       'post[state]': '0',
                       'post[photoset_layout]': '1',
                       'post[photoset_order]': 'o1',
                       'images[o1]': self.QI['OTHER_FIELD']['image_link']}
            headers = {'Content-type': 'application/json', 'Accept': 'application/json, text/javascript, */*'}
            r = s.post(url, data=json.dumps(payload), headers=headers)
            if r.status_code!=200:
                raise Exception('unexpected response: %s : %s'%(url, r.status_code))
            # check success
            response_json = json.loads(r.text)
            if 'errors' in response_json and response_json['errors']:
                raise Exception('handle failed: %s'%r.text)
        else:
            raise Exception('wrong type: %d'%self.QI['TYPE'])
            return
        self.session = s
