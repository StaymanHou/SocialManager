from basic_posterhandler import *
import time
from time import sleep, time
from selenium import webdriver
from selenium.webdriver.common.keys import Keys
import logging
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
        try: elem = browser.find_element_by_id('loginbutton')
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
            elem.send_keys(imgdir+queueitem['IMAGE_FILE'])
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


        
