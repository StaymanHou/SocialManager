from basic_posterhandler import *
import time
from time import sleep
from base64 import b64encode
import urllib
import requests
import re
from lxml import etree
import logging
from MyQueue import *
from RssPost import *
from Tags import *
from MyDict import STATUS_DICT
from systemHelper import specialize_path

# check required accset['OTHER_SETTING']
if 'page_path' not in accset['OTHER_SETTING'] or not accset['OTHER_SETTING']['page_path'].strip():
    logging.warn('facebook post handle page_path not specified! e.g. (https://m.facebook.com)"/pages/Staymancom/203819689790928"')
    return 0
if 'page_id' not in accset['OTHER_SETTING'] or not accset['OTHER_SETTING']['page_id'].strip():
    logging.warn('facebook post handle page_id not specified! e.g. "203819689790928"')
    return 0

# get login page
s = requests.Session()
url = 'https://m.facebook.com/login.php'
try: r = s.get(url)
except Exception, e: logging.warn('facebook post handle no response: %s : %s'%(url, e)); return 0
htmltree = etree.HTML(r.text.encode('ascii', 'ignore'))
elems = htmltree.xpath('//input[@name="lsd"]')
if len(elems) == 0: logging.warn('facebook login error: lsd not found.'); return 0
lsd = elems[0].attrib['value']
elems = htmltree.xpath('//input[@name="li"]')
if len(elems) == 0: logging.warn('facebook login error: li not found.'); return 0
li = elems[0].attrib['value']
sleep(load_iteration)

# post login
url = 'https://m.facebook.com/login.php?refsrc=https%3A%2F%2Fm.facebook.com%2Flogin.php&refid=9'
payload = {'width': 0,
            'version': 1,
            'signup_layout': 'layout|bottom_clean||wider_form||prmnt_btn|special||st|create||header_crt_acct_button||hdbtn_color|green||signupinstr||launched_Mar3',
            'pxr': 0,
            'pass': 'Hyc8909162416', # accset['PSWD'],
            'm_ts': int(time.time()),
            'lsd': lsd,
            'login': 'Log In',
            'li': li,
            'gps': 0,
            'email': 'hhyycc0418@gmail.com', # accset['USERNAME']
            'charset_test': '€,´,€,´,水,Д,Є',
            'ajax': 0}
try: r = s.post(url, data=payload)
except Exception, e: logging.warn('facebook post handle no response: %s : %s'%(url, e)); return 0
if r.status_code!=200: logging.warn('facebook post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
sleep(load_iteration)

# switch to page
url = 'https://m.facebook.com'+'/pages/Staymancom/203819689790928'
# url = 'https://m.facebook.com' + accset['OTHER_SETTING']['page_path'].strip()
try: r = s.get(url)
except Exception, e: logging.warn('facebook post handle no response: %s : %s'%(url, e)); return 0
if r.status_code!=200: logging.warn('facebook post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
htmltree = etree.HTML(r.text.encode('ascii', 'ignore'))
elems = htmltree.xpath('//a[text()="Change"]')
if len(elems) == 0: logging.warn('facebook switch page error: a[text()="Change"] not found.'); return 0
url = 'https://m.facebook.com' + elems[0].attrib['href']

# act as page
try: r = s.get(url)
except Exception, e: logging.warn('facebook post handle no response: %s : %s'%(url, e)); return 0
if r.status_code!=200: logging.warn('facebook post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
htmltree = etree.HTML(r.text.encode('ascii', 'ignore'))
elems = htmltree.xpath('//input[@name="fb_dtsg"]')
if len(elems) == 0: logging.warn('facebook act as error: fb_dtsg not found.'); return 0
fb_dtsg = elems[0].attrib['value']

# link post
url = 'https://m.facebook.com/a/home.php?refid=17'
payload = {'xhpc_timeline': True,
            'users_with': '',
            'update': 'Share',
            'timestamp': '',
            'target': '203819689790928', # accset['OTHER_SETTING']['page_id']
            'status': 'its working',
            'r2a': True,
            'privacy': '',
            'photos': '',
            'linkUrl': '',
            'linkTitle': '',
            'linkThumbnail': '',
            'linkSummary': '',
            'granularity': '',
            'finch': '',
            'fb_dtsg': fb_dtsg,
            'disable_location_sharing': '',
            'charset_test': '€,´,€,´,水,Д,Є'}
try: r = s.post(url, data=payload)
except Exception, e: logging.warn('facebook post handle no response: %s : %s'%(url, e)); return 0
if r.status_code!=200: logging.warn('facebook post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
return 1

# photo post
if not queueitem['IMAGE_FILE']: logging.warn('No image file specified in a image type tweet.'); return 0
url = 'https://m.facebook.com/photos/upload/?upload_source=advanced_composer&max_allowed=3&target_id=%s&ref=hl'%accset['OTHER_SETTING']['page_id']
try: r = s.get(url)
except Exception, e: logging.warn('facebook post handle no response: %s : %s'%(url, e)); return 0
if r.status_code!=200: logging.warn('facebook post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
htmltree = etree.HTML(r.text.encode('ascii', 'ignore'))
elems = htmltree.xpath('//input[@name="fb_dtsg"]')
if len(elems) == 0: logging.warn('facebook photo post error: fb_dtsg not found.'); return 0
fb_dtsg = elems[0].attrib['value']
elems = htmltree.xpath('//input[@name="return_uri"]')
if len(elems) == 0: logging.warn('facebook photo post error: return_uri not found.'); return 0
return_uri = elems[0].attrib['value']
elems = htmltree.xpath('//input[@name="return_uri_error"]')
if len(elems) == 0: logging.warn('facebook photo post error: return_uri_error not found.'); return 0
return_uri_error = elems[0].attrib['value']
elems = htmltree.xpath('//form[@method="post"]')
if len(elems) == 0: logging.warn('facebook photo post error: form not found.'); return 0
url = elems[0].attrib['action']

# commit post
content = addhashtag(queueitem['CONTENT'], queueitem['TAG'], mode = 1) + '\n\nRead more:\n' + queueitem['LINK']
payload = {'fb_dtsg': fb_dtsg,
            'charset_test': '€,´,€,´,水,Д,Є',
            # 'file1': b64encode(open(imgdir+queueitem['IMAGE_FILE'], 'rb').read()),
            'caption': content,
            'return_uri': return_uri,
            'return_uri_error': return_uri_error,
            'target': accset['OTHER_SETTING']['page_id'],
            'ref': 'm_upload_pic',
            'album_fbid': ''}
files = {'file1': open(imgdir+queueitem['IMAGE_FILE'], 'rb')}
try: r = s.post(url, data=payload, files=files)
except Exception, e: logging.warn('facebook post handle no response: %s : %s'%(url, e)); return 0
if r.status_code!=200: logging.warn('facebook post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
return 1


            # elem.send_keys(queueitem['LINK'])
            # elem.send_keys(Keys.ENTER)
            # sleep(10)
            # elem.clear()
            # content = addhashtag(queueitem['CONTENT'], queueitem['TAG'], mode = 1)
            # elem.send_keys(content)


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
            myqueue['CONTENT'] = lastrp['TITLE']
            myqueue['TAG'] = lastrp['TAG']
            myqueue['LINK'] = lastrp['LINK']
            myqueue['IMAGE_FILE'] = lastrp['IMAGE_FILE']
            myqueue['RSS_SOURCE_PK'] = lastrp['PK']
            if (myqueue['IMAGE_FILE'] is None) or (myqueue['IMAGE_FILE']==''): myqueue['TYPE'] = 1
            myqueue.save()
            return
        else:
            pass
        return

    # override
    def post_handle(self, accset, queueitem, imgdir, load_iteration=1):
        browser = webdriver.Firefox()
        # login
        try: browser.get('http://facebook.com')
        except: browser.quit(); logging.warn('facebook post handle error 1'); return 0
        sleep(load_iteration)
        try: elem = browser.find_element_by_id('email')
        except: browser.quit(); logging.warn('facebook post handle error 2'); return 0
        elem.send_keys(accset['USERNAME'])
        try: elem = browser.find_element_by_id('pass')
        except: browser.quit(); logging.warn('facebook post handle error 3'); return 0
        elem.send_keys(accset['PSWD'])
        try: elem = browser.find_element_by_xpath('//*[@id="loginbutton"]')
        except: browser.quit(); logging.warn('facebook post handle error 4'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('facebook post handle error 5'); return 0
        sleep(load_iteration)
        # switch identity
        try: elem = browser.find_element_by_id('userNavigationLabel')
        except: browser.quit(); logging.warn('facebook post handle error 6'); return 0
        elem.click()
        try: elem = browser.find_element_by_xpath('//a[./div/div/div/text()="%s"]'%str(accset['OTHER_SETTING']['page_name']))
        except: browser.quit(); logging.warn('facebook post handle error 7'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('facebook post handle error 8'); return 0
        sleep(load_iteration)
        if (queueitem['TYPE']==2) and (queueitem['IMAGE_FILE'] is not None) and (queueitem['IMAGE_FILE'].strip()!=''):
        # type 2
            try: elems = browser.find_elements_by_xpath('//a[@data-endpoint="/ajax/composerx/attachment/media/chooser/"]')
            except: browser.quit(); logging.warn('facebook post handle error 9'); return 0
            elem = None
            for e in elems:
                if e.is_displayed():
                    elem = e
                    break
            if elem is None: browser.quit(); logging.warn('facebook post handle error 10'); return 0
            elem.click()
            sleep(load_iteration)
            try: elems = browser.find_elements_by_xpath('//div[text()="Upload Photos/Video"]')
            except: browser.quit(); logging.warn('facebook post handle error 11'); return 0
            elem = None
            for e in elems:
                if e.is_displayed():
                    elem = e
                    break
            if elem is None: browser.quit(); logging.warn('facebook post handle error 12'); return 0
            elem.click()
            sleep(load_iteration)
            try: elems = browser.find_elements_by_xpath('//input[@name="file1"]')
            except: browser.quit(); logging.warn('facebook post handle error 13'); return 0
            for e in elems:
                if e.is_displayed():
                    elem = e
                    break
            if elem is None: browser.quit(); logging.warn('facebook post handle error 14'); return 0
            if not elem.is_displayed(): browser.quit(); logging.warn('facebook post handle error 14.5'); return 0
            elem.send_keys(specialize_path(imgdir+queueitem['IMAGE_FILE']))
            try: elems = browser.find_elements_by_xpath('//textarea[@name="xhpc_message_text"]')
            except: browser.quit(); logging.warn('facebook post handle error 15'); return 0
            elem = None
            for e in elems:
                if e.is_displayed():
                    elem = e
                    break
            if elem is None: browser.quit(); logging.warn('facebook post handle error 16'); return 0
            content = addhashtag(queueitem['CONTENT'], queueitem['TAG'], mode = 1)
            elem.send_keys(content+'\n\nRead more:\n'+queueitem['LINK'])
            try: elems = browser.find_elements_by_xpath('//button[./span[text()="Post"]]')
            except: browser.quit(); logging.warn('facebook post handle error 17'); return 0
            elem = None
            for e in elems:
                if e.is_displayed():
                    elem = e
                    break
            if elem is None: browser.quit(); logging.warn('facebook post handle error 18'); return 0
            elem.click()
            # check success
            sleep(15)
            try: elem = browser.find_element_by_xpath('//abbr[contains(text(), "seconds ago")]')
            except: browser.quit(); logging.warn('facebook post handle error 19'); return 0
            browser.quit()
            return 1
        else:
            # type 1
            try: elems = browser.find_elements_by_xpath('//textarea[@name="xhpc_message"]')
            except: browser.quit(); logging.warn('facebook post handle error 20'); return 0
            elem = None
            for e in elems:
                if e.is_displayed():
                    elem = e
                    break
            if elem is None: browser.quit(); logging.warn('facebook post handle error 21'); return 0
            elem.click()
            sleep(load_iteration)
            elem.send_keys(queueitem['LINK'])
            elem.send_keys(Keys.ENTER)
            sleep(10)
            elem.clear()
            content = addhashtag(queueitem['CONTENT'], queueitem['TAG'], mode = 1)
            elem.send_keys(content)
            try: elems = browser.find_elements_by_xpath('//button[text()="Post"]')
            except: browser.quit(); logging.warn('facebook post handle error 22'); return 0
            elem = None
            for e in elems:
                if e.is_displayed():
                    elem = e
                    break
            if elem is None: browser.quit(); logging.warn('facebook post handle error 23'); return 0
            elem.click()
            # check success
            sleep(10)
            try: elem = browser.find_element_by_xpath('//abbr[contains(text(), "seconds ago")]')
            except:
                browser.quit();
                sleep(load_iteration)
                logging.warn('facebook post handle error 24'); return 0
            browser.quit()
            return 1


        
