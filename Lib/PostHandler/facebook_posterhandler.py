# coding: utf-8

from basic_posterhandler import *
import time
from time import sleep
import requests
from lxml import etree
from selenium import webdriver
from selenium.webdriver.common.keys import Keys
import logging
import pickle
from ..MyQueue import *
from ..RssPost import *
from ..Tags import *

class handler(basicposterhandler):

    def __init__(self):
        super(handler, self).__init__()
        self.module_name = 'facebook'

    # override
    def without_session(self, load_iteration=1):
        if self.session is None:
            self.session = {1: None, 2: None}
        if self.QI['TYPE']==1:
            # type link
            self.browser = webdriver.Firefox()
            try:
                self.inner_without_session(load_iteration)
            except Exception, e:
                raise e
            finally:
                self.browser.quit()
        elif self.QI['TYPE']==2 and self.QI['IMAGE_LINK']:
            # type image
            # get login page
            s = requests.Session()
            url = 'https://m.facebook.com/login.php'
            r = s.get(url)
            htmltree = etree.HTML(r.text.encode('ascii', 'ignore'))
            elems = htmltree.xpath('//input[@name="lsd"]')
            if len(elems) == 0:
                raise Exception('facebook login error: lsd not found.')
            lsd = elems[0].attrib['value']
            elems = htmltree.xpath('//input[@name="li"]')
            if len(elems) == 0:
                raise Exception('facebook login error: li not found.')
            li = elems[0].attrib['value']
            sleep(load_iteration)

            # post login
            url = 'https://m.facebook.com/login.php?refsrc=https%3A%2F%2Fm.facebook.com%2Flogin.php&refid=9'
            payload = {'width': 0,
                        'version': 1,
                        'signup_layout': 'layout|bottom_clean||wider_form||prmnt_btn|special||st|create||header_crt_acct_button||hdbtn_color|green||signupinstr||launched_Mar3',
                        'pxr': 0,
                        'pass': self.AccSet['PSWD'],
                        'm_ts': int(time.time()),
                        'lsd': lsd,
                        'login': 'Log In',
                        'li': li,
                        'gps': 0,
                        'email': self.AccSet['USERNAME'],
                        'charset_test': '€,´,€,´,水,Д,Є',
                        'ajax': 0}
            r = s.post(url, data=payload)
            if r.status_code!=200:
                raise Exception('facebook post handle unexpected response: %s : %s'%(url, r.status_code))
            sleep(load_iteration)
            self.session[2] = s
            self.with_session
        else:
            raise Exception('wrong type: %d'%self.QI['TYPE'])

    # override
    def with_session(self, load_iteration=1):
        if self.session[self.QI['TYPE']] is None:
            self.without_session(load_iteration)
            return
        if self.QI['TYPE']==1:
            # type link
            self.browser = webdriver.Firefox()
            try:
                self.inner_without_session(load_iteration)
            except Exception, e:
                raise e
            finally:
                self.browser.quit()
        elif self.QI['TYPE']==2 and self.QI['IMAGE_LINK']:
            s = self.session[2]

            # type image
            self.garentee_imgfile()
            imgfile = open(self.temp_img_dir+self.QI['IMAGE_FILE'], 'rb')

            # switch to page
            url = 'https://m.facebook.com' + self.AccSet['OTHER_SETTING']['page_path'].strip()
            r = s.get(url)
            if r.status_code!=200:
                raise Exception('facebook post handle unexpected response: %s : %s'%(url, r.status_code))
            htmltree = etree.HTML(r.text.encode('ascii', 'ignore'))
            elems = htmltree.xpath('//a[text()="Change"]')
            if len(elems) == 0:
                raise Exception('facebook switch page error: a[text()="Change"] not found.')
            url = 'https://m.facebook.com' + elems[0].attrib['href']
            voice_flag = False
            elems = htmltree.xpath('//span[@class="name" and contains(text(), "%s")]'%self.AccSet['OTHER_SETTING']['page_name'])
            if len(elems) == 0:
                voice_flag = True

            # act as page
            if voice_flag:
                r = s.get(url)
                if r.status_code!=200:
                    raise Exception('facebook post handle unexpected response: %s : %s'%(url, r.status_code))

            # photo post
            url = 'https://m.facebook.com/photos/upload/?upload_source=advanced_composer&max_allowed=3&target_id=%s&ref=hl'%self.AccSet['OTHER_SETTING']['page_id']
            r = s.get(url)
            if r.status_code!=200:
                raise Exception('facebook post handle unexpected response: %s : %s'%(url, r.status_code))
            htmltree = etree.HTML(r.text.encode('ascii', 'ignore'))
            elems = htmltree.xpath('//input[@name="fb_dtsg"]')
            if len(elems) == 0:
                raise Exception('facebook photo post error: fb_dtsg not found.')
            fb_dtsg = elems[0].attrib['value']
            elems = htmltree.xpath('//input[@name="return_uri"]')
            if len(elems) == 0:
                raise Exception('facebook photo post error: return_uri not found.')
            return_uri = elems[0].attrib['value']
            elems = htmltree.xpath('//input[@name="return_uri_error"]')
            if len(elems) == 0:
                raise Exception('facebook photo post error: return_uri_error not found.')
            return_uri_error = elems[0].attrib['value']
            elems = htmltree.xpath('//form[@method="post"]')
            if len(elems) == 0:
                raise Exception('facebook photo post error: form not found.')
            url = elems[0].attrib['action']

            # commit post
            content = addhashtag(self.QI['CONTENT'], self.QI['TAG'], mode = 1) + '\n\nRead more:\n' + self.QI['LINK']
            payload = {'fb_dtsg': fb_dtsg,
                        'charset_test': '€,´,€,´,水,Д,Є',
                        'caption': content,
                        'return_uri': return_uri,
                        'return_uri_error': return_uri_error,
                        'target': self.AccSet['OTHER_SETTING']['page_id'],
                        'ref': 'm_upload_pic',
                        'album_fbid': ''}
            files = {'file1': imgfile}
            r = s.post(url, data=payload, files=files)
            if r.status_code!=200:
                raise Exception('facebook post handle unexpected response: %s : %s'%(url, r.status_code))
            self.session[2] = s
        else:
            raise Exception('wrong type: %d'%self.QI['TYPE'])

    def inner_without_session(self, load_iteration=1):
        # type link
        # login
        self.browser.get('http://facebook.com')
        sleep(load_iteration)
        elem = self.browser.find_element_by_id('email')
        elem.send_keys(self.AccSet['USERNAME'])
        elem = self.browser.find_element_by_id('pass')
        elem.send_keys(self.AccSet['PSWD'])
        elem = self.browser.find_element_by_id('persist_box')
        elem.click()
        elem = self.browser.find_element_by_xpath('//*[@id="loginbutton"]')
        elem.click()
        sleep(load_iteration)
        # switch identity
        elem = self.browser.find_element_by_id('userNavigationLabel')
        elem.click()
        elem = self.browser.find_element_by_xpath('//a[./div/div/div/text()="%s"]'%str(self.AccSet['OTHER_SETTING']['page_name']))
        elem.click()
        sleep(10)
        self.session[1] = pickle.dumps([ cookie for cookie in self.browser.get_cookies() if cookie['domain'] == '.facebook.com'])
        self.post(load_iteration)

    def inner_with_session(self, load_iteration=1):
        # load session cookie
        self.browser.get('https://www.facebook.com')
        cookies = pickle.loads(self.session[1])
        for cookie in cookies:
            browser.add_cookie(cookie)
        self.browser.get('https://www.facebook.com'+self.AccSet['OTHER_SETTING']['page_path'].strip())
        sleep(load_iteration)
        self.post(load_iteration)
            
    def post(self, load_iteration=1):
        # post
        elems = self.browser.find_elements_by_xpath('//textarea[@name="xhpc_message"]')
        elem = None
        for e in elems:
            if e.is_displayed():
                elem = e
                break
        if elem is None:
            raise Exception('facebook post handle error: can\'t find //textarea[@name="xhpc_message"]')
        elem.click()
        sleep(load_iteration)
        elem.send_keys(self.QI['LINK'])
        elem.send_keys(Keys.ENTER)
        sleep(10)
        elem.clear()
        content = addhashtag(self.QI['CONTENT'], self.QI['TAG'], mode = 1)
        elem.send_keys(content)
        elems = self.browser.find_elements_by_xpath('//button[text()="Post"]')
        elem = None
        for e in elems:
            if e.is_displayed():
                elem = e
                break
        if elem is None:
            raise Exception('facebook post handle error: can\'t find //button[text()="Post"]')
        elem.click()
        # wait a while
        sleep(10)
        self.session[1] = pickle.dumps([ cookie for cookie in self.browser.get_cookies() if cookie['domain'] == '.facebook.com'])
