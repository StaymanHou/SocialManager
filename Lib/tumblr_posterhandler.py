from basic_posterhandler import *
import time
from time import sleep
import urllib
import requests
import logging
import json
from lxml import etree
from MyQueue import *
from RssPost import *
from Tags import *
from MyDict import STATUS_DICT

class handler(basicposterhandler):

    # override
    def auto_mode_handle(self, acc, accset, am):
        if am['CODE']==1:
            return
        elif am['CODE']==2:
            myqueue = MyQueue()
            myqueue.GetPendingFirst(acc['PK'], am['MODULE'])
            if myqueue['PK'] is not None: return
            lastrp = RssPost.GetLatest(acc['PK'], am['MODULE'])
            if lastrp['PK'] is None: return
            myqueue['STATUS'] = STATUS_DICT['Pending']
            myqueue['ACCOUNT'] = acc['PK']
            myqueue['MODULE'] = am['MODULE']
            myqueue['TYPE'] = 2
            myqueue['TITLE'] = lastrp['TITLE']
            myqueue['CONTENT'] = lastrp['DESCRIPTION']
            myqueue['EXTRA_CONTENT'] = accset['EXTRA_CONTENT']
            myqueue['TAG'] = lastrp['TAG']
            myqueue['LINK'] = lastrp['LINK']
            myqueue['IMAGE_FILE'] = lastrp['IMAGE_FILE']
            blog_name = ''
            if 'blog_name' in accset['OTHER_SETTING']: blog_name = accset['OTHER_SETTING']['blog_name']
            link_anchor_text = ''
            if 'link_anchor_text' in accset['OTHER_SETTING']: link_anchor_text = accset['OTHER_SETTING']['link_anchor_text']
            myqueue['OTHER_FIELD'] = {'blog_name': blog_name,'image_link': lastrp['IMAGE_LINK'],'link_anchor_text': link_anchor_text}
            myqueue['RSS_SOURCE_PK'] = lastrp['PK']
            if (myqueue['IMAGE_FILE'] is None) or (myqueue['IMAGE_FILE']==''): myqueue['TYPE'] = 1
            myqueue.save()
            return
        else:
            pass
        return

    # override
    def post_handle(self, accset, queueitem, imgdir, load_iteration=1):
        s = requests.Session()
        # visit login page and get cookie
        url = 'https://www.tumblr.com/login'
        try: r = s.get(url)
        except Exception, e: logging.warn('tumblr post handle no response: %s : %s'%(url, e)); return 0
        if r.status_code!=200: logging.warn('tumblr post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
        htmltree = etree.HTML(r.text)
        elem = htmltree.xpath('//input[@name="recaptcha_public_key"]')
        if len(elem)==0:  logging.warn('tumblr can\'t get //input[@name="recaptcha_public_key"]'); return 0
        recaptcha_public_key = elem[0].get('value')
        elem = htmltree.xpath('//input[@name="form_key"]')
        if len(elem)==0:  logging.warn('tumblr can\'t get //input[@name="form_key"]'); return 0
        capture = form_key = elem[0].get('value')
        elems = htmltree.xpath('//img[@style and @src]')
        for elem in elems:
            url = elem.get('src')
            sleep(load_iteration)
            try: r = s.get(url)
            except Exception, e: logging.warn('tumblr post handle no response: %s : %s'%(url, e)); return 0
            if r.status_code!=200: logging.warn('tumblr post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
        addcookie = {'capture': capture}
        # login
        url = 'https://www.tumblr.com/login'
        payload = {'user[password]': accset['PSWD'],
                   'user[email]': accset['USERNAME'],
                   'user[age]': '',
                   'used_suggestion': '0',
                   'tumblelog[name]': '',
                   'seen_suggestion': '0',
                   'recaptcha_response_field': '',
                   'recaptcha_public_key': recaptcha_public_key,
                   'form_key': form_key,
                   'context': 'no_referer'}
        try: r = s.post(url, data=payload, cookies=addcookie)
        except Exception, e: logging.warn('tumblr post handle no response: %s : %s'%(url, e)); return 0
        if r.status_code!=200: logging.warn('tumblr post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
        sleep(load_iteration)        
        # go to dashboard
        url = 'https://www.tumblr.com/dashboard'
        try: r = s.get(url)
        except Exception, e: logging.warn('tumblr post handle no response: %s : %s'%(url, e)); return 0
        if r.status_code!=200: logging.warn('tumblr post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
        # extract form key, blog_name
        htmltree = etree.HTML(r.text)
        elem = htmltree.xpath('//form[@id="search_form"]/input[@name="form_key"]')
        if len(elem)==0:  logging.warn('tumblr can\'t get //form[@id="search_form"]/input[@name="form_key"]'); return 0
        form_key = elem[0].get('value')
        elem = htmltree.xpath('//form[@id="search_form"]/input[@name="t"]')
        if len(elem)==0:  logging.warn('tumblr can\'t get //form[@id="search_form"]/input[@name="t"]'); return 0
        blog_name = elem[0].get('value')
        if ('blog_name' in accset['OTHER_SETTING']) and (accset['OTHER_SETTING']['blog_name'] is not None) and (accset['OTHER_SETTING']['blog_name'].strip()!=''):
            blog_name = accset['OTHER_SETTING']['blog_name'].strip()        
        if ('blog_name' in queueitem['OTHER_FIELD']) and (queueitem['OTHER_FIELD']['blog_name'] is not None) and (queueitem['OTHER_FIELD']['blog_name'].strip()!=''):
            blog_name = queueitem['OTHER_FIELD']['blog_name'].strip()
        sleep(load_iteration)
        if (queueitem['TYPE']==2) and ('image_link' in queueitem['OTHER_FIELD']) and (queueitem['OTHER_FIELD']['image_link'] is not None) and (queueitem['OTHER_FIELD']['image_link'].strip()!=''):
            # type 2 = photo
            link_anchor_text = queueitem['LINK']
            if ('link_anchor_text' in accset['OTHER_SETTING'] and accset['OTHER_SETTING']['link_anchor_text'] is not None and accset['OTHER_SETTING']['link_anchor_text'].strip()!=''):
                link_anchor_text = accset['OTHER_SETTING']['link_anchor_text'].strip()
            if ('link_anchor_text' in queueitem['OTHER_FIELD'] and queueitem['OTHER_FIELD']['link_anchor_text'] is not None and queueitem['OTHER_FIELD']['link_anchor_text'].strip()!=''):
                link_anchor_text = queueitem['OTHER_FIELD']['link_anchor_text'].strip()
            content = '<p><strong>'+queueitem['TITLE']+'</strong></p><p></p>'+queueitem['CONTENT']+'<p><em><a href="'+queueitem['LINK']+'">'+link_anchor_text+'</a></em></p>'+queueitem['EXTRA_CONTENT']
            if queueitem['TAG'] is None: tag = ''
            else: tag = ','.join([tag.strip() for tag in queueitem['TAG'].split(',')])
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
                       'post[three]': queueitem['LINK'],# click through link
                       'MAX_FILE_SIZE': '10485760',
                       'post[type]': 'photo',
                       'post[two]': content,# content
                       'post[tags]': tag,# tag list
                       'post[publish_on]': '',
                       'post[state]': '0',
                       'post[photoset_layout]': '1',
                       'post[photoset_order]': 'o1',
                       'images[o1]': queueitem['OTHER_FIELD']['image_link']}
            headers = {'Content-type': 'application/json', 'Accept': 'application/json, text/javascript, */*'}
            try: r = s.post(url, data=json.dumps(payload), headers=headers)
            except Exception, e: logging.warn('tumblr post handle no response: %s : %s'%(url, e)); return 0
            if r.status_code!=200: logging.warn('tumblr post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
            # check success
            response_json = json.loads(r.text)
            if 'errors' in response_json and response_json['errors']: logging.warn('tumblr post handle failed: %s'%r.text); return 0
            return 1
        else:
            # type 1 = link
            # get data of link
            url = 'http://www.tumblr.com/svc/post/fetch_og'
            payload = {'form_key': form_key,
                       'url': queueitem['LINK']}
            headers = {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            try: r = s.post(url, data=urllib.urlencode(payload), headers=headers)           
            except Exception, e: logging.warn('tumblr post handle no response: %s : %s'%(url, e)); return 0
            if r.status_code!=200: logging.warn('tumblr post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
            response_json = json.loads(r.text)
            # organize content
            thumbnail = ''
            if ('image_link' in queueitem['OTHER_FIELD']) and (queueitem['OTHER_FIELD']['image_link'] is not None) and (queueitem['OTHER_FIELD']['image_link'].strip()!=''): thumbnail = queueitem['OTHER_FIELD']['image_link'].strip()
            title = queueitem['TITLE']
            content = queueitem['CONTENT']
            if queueitem['TAG'] is None: tag = ''
            else: tag = ','.join([tag.strip() for tag in queueitem['TAG'].split(',')])
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
            content += queueitem['EXTRA_CONTENT']
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
                       'post[two]': queueitem['LINK'],
                       'post[one]': title,#title
                       'post[three]': content,#content change
                       'post[tags]': tag,# change what if 2, ','.join
                       'post[publish_on]': '',
                       'post[state]': '0'}
            headers = {'Content-type': 'application/json', 'Accept': 'application/json, text/javascript, */*'}
            try: r = s.post(url, data=json.dumps(payload), headers=headers)
            except Exception, e: logging.warn('tumblr post handle no response: %s : %s'%(url, e)); return 0
            if r.status_code!=200: logging.warn('tumblr post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
            # check success
            response_json = json.loads(r.text)
            if 'errors' in response_json and response_json['errors']: logging.warn('tumblr post handle failed: %s'%r.text); return 0
            return 1        
        
