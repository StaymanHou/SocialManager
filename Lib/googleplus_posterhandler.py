from basic_posterhandler import *
from selenium import webdriver
#from selenium.webdriver import ActionChains
#from selenium.webdriver.remote.command import Command
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
            myqueue['TYPE'] = 1
            myqueue['TITLE'] = lastrp['TITLE']
            myqueue['CONTENT'] = lastrp['TITLE']
            myqueue['TAG'] = lastrp['TAG']
            myqueue['LINK'] = lastrp['LINK']
            myqueue['IMAGE_FILE'] = lastrp['IMAGE_FILE']
            myqueue['OTHER_FIELD'] = {"circle_name_list": accset['OTHER_SETTING']['circle_name_list']}
            myqueue['RSS_SOURCE_PK'] = lastrp['PK']
            myqueue.save()
            return
        else:
            pass
        return

    # override
    def post_handle(self, accset, queueitem, imgdir, load_iteration=1):
        browser = webdriver.Firefox()
        # front page
        try: browser.get('https://www.google.com/')
        except: browser.quit(); logging.warn('google+ post handle error 1'); return 0
        sleep(load_iteration)
        try: elem = browser.find_element_by_xpath('//span[text()="Sign in"]')
        except: browser.quit(); logging.warn('google+ post handle error 2'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('google+ post handle error 3'); return 0
        sleep(load_iteration)
        # login
        try: elem = browser.find_element_by_id('Email')
        except: browser.quit(); logging.warn('google+ post handle error 4'); return 0
        elem.send_keys(accset['USERNAME'])
        try: elem = browser.find_element_by_id('Passwd')
        except: browser.quit(); logging.warn('google+ post handle error 5'); return 0
        elem.send_keys(accset['PSWD'])
        try: elem = browser.find_element_by_id('PersistentCookie')
        except: browser.quit(); logging.warn('google+ post handle error 6'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('google+ post handle error 7'); return 0
        try: elem = browser.find_element_by_id('signIn')
        except: browser.quit(); logging.warn('google+ post handle error 8'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('google+ post handle error 9'); return 0
        sleep(load_iteration)
        # try: elem = browser.find_element_by_xpath('//span[contains(text(), "+")]')
        # except: browser.quit(); logging.warn('google+ post handle error 10'); return 0
        # try: elem.click()
        # except: browser.quit(); logging.warn('google+ post handle error 11'); return 0
        try:
            browser.get('https://plus.google.com/u/0/')
        except:
            browser.quit(); logging.warn('google+ post handle error 10.5'); return 0
        sleep(10)
        # switch identity
        try: elem = browser.find_element_by_xpath('//div[text()="Home"]')
        except: browser.quit(); logging.warn('google+ post handle error 12'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('google+ post handle error 13'); return 0
        sleep(1)
        try: elem = browser.find_element_by_xpath('//div[text()="Pages"]')
        except: browser.quit(); logging.warn('google+ post handle error 14'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('google+ post handle error 15'); return 0
        sleep(10)
        try: elem = browser.find_element_by_xpath('//div[../div[1]/div[2]/div/div[1]/div/span/text()="%s"]/a'%str(accset['OTHER_SETTING']['page_name']))
        except: browser.quit(); logging.warn('google+ post handle error 16'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('google+ post handle error 17'); return 0
        sleep(10)
        # share dialog
        try: elem = browser.find_element_by_xpath('//div[text()="Share"]')
        except: browser.quit(); logging.warn('google+ post handle error 18'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('google+ post handle error 19'); return 0
        sleep(10)
        if (queueitem['TYPE'] == 2) and (queueitem['IMAGE_FILE'] is not None) and (queueitem['IMAGE_FILE'].strip()!=''):
            # type 2
            #try: browser.switch_to_frame(browser.find_element_by_xpath('//iframe[@title="Share"]'))
            #except: browser.quit(); logging.warn('google+ post handle error 40'); return 0
            #try: elem = browser.find_element_by_xpath('//span[@title="Add photos"]')
            #except: browser.quit(); logging.warn('google+ post handle error 41'); return 0
            #try: elem.click()
            #sleep(2)
            #try: browser.execute_script('var img = document.createElement("img"); img.setAttribute("src", "http://images.scienceworldreport.com/data/images/full/4238/tanning.jpg"); img.id = "smuploading"; document.body.children[1].appendChild(img);')
            #except: browser.quit(); logging.warn('google+ post handle error 42'); return 0
            #sleep(2)
            #try: source = browser.find_element_by_xpath('//img[@id="smuploading"]')
            #except: browser.quit(); logging.warn('google+ post handle error 43'); return 0
            #try: target = browser.find_element_by_xpath('//div[text()="Drag photos here"]')
            #except: browser.quit(); logging.warn('google+ post handle error 44'); return 0
            #actionChains = ActionChains(browser)
            #actionChains.click_and_hold(source).release(target)
            #actionChains.move_to_element_with_offset(source,5,5).click_and_hold().move_to_element_with_offset(target,5,5).release().perform()
            #action_chains = ActionChains(browser)
            #action_chains.drag_and_drop(source, target)
            #ActionChains(browser).move_to_element_with_offset(source,5,5).click_and_hold().move_to_element_with_offset(target,5,5).release().perform()
            
            
            #ActionChains(browser).drag_and_drop(source, target).perform()
            #source.move_to_element(target)
            
            browser.quit(); logging.warn('google+ post handle error 20'); return 0
            #browser.execute_script
            #'var img = document.createElement("img"); img.setAttribute("src", "http://images.scienceworldreport.com/data/images/full/4238/tanning.jpg").setAttribute("id", "smuploadimg"); document.body.appendChild(img);'
        else:
            # type 1
            # try: browser.switch_to_frame(browser.find_element_by_xpath('//iframe[@title="Share"]'))
            try: browser.switch_to_frame(browser.find_element_by_xpath('//iframe[../../div/div[1]/a/div/text()="Share"]'))
            except: browser.quit(); logging.warn('google+ post handle error 21'); return 0
            try: elem = browser.find_element_by_xpath('//span[@title="Add link"]')
            except: browser.quit(); logging.warn('google+ post handle error 22'); return 0
            try: elem.click()
            except: browser.quit(); logging.warn('google+ post handle error 23'); return 0
            try: elem = browser.find_element_by_xpath('//input[../div/text()="Enter or paste a link"]')
            except: browser.quit(); logging.warn('google+ post handle error 24'); return 0
            elem.send_keys(queueitem['LINK'])
            try: elem = browser.find_element_by_xpath('//div[text()="Add"]')
            except: browser.quit(); logging.warn('google+ post handle error 25'); return 0
            try: elem.click()
            except: browser.quit(); logging.warn('google+ post handle error 26'); return 0
            sleep(10)
            try: elem = browser.find_element_by_xpath('//div[2][../div/text()="Share what\'s new..."]')
            except: browser.quit(); logging.warn('google+ post handle error 27'); return 0
            content = addhashtag(queueitem['CONTENT'], queueitem['TAG'], mode = 1)
            elem.send_keys(content)
        # common part
            # delete everything first
        # the circle selection dialog is not clickable any more
        # try: elems = browser.find_elements_by_xpath('//div[./span[2]/span/span/text()="+ Add more people"]/span[1]/span/span[1]/div[1]/span[1]')
        # except: browser.quit(); logging.warn('google+ post handle error 28'); return 0
        # for elem in elems:
        #     try: elem.click()
        #     except: browser.quit(); logging.warn('google+ post handle error 29'); return 0
        #     sleep(1)
            # create new
        # try: elems = browser.find_elements_by_xpath('//div[./span[2]/input[@placeholder="+ Add names, circles, or email addresses"]]')
        # except: browser.quit(); logging.warn('google+ post handle error 30'); return 0
        # elem = None
        # for e in elems:
        #     if e.is_displayed():
        #         elem = e
        #         break
        # try: elem.click()
        # except: browser.quit(); logging.warn('google+ post handle error 31'); return 0
        # try: elem = browser.find_element_by_xpath('//span[text()="Public"]')
        # except: browser.quit(); logging.warn('google+ post handle error 32'); return 0
        # try: elem.click()
        # except: browser.quit(); logging.warn('google+ post handle error 33'); return 0
        # for circle_name in queueitem['OTHER_FIELD']['circle_name_list']:
        #     try: elem = browser.find_element_by_xpath('//span[text()="%s"]'%circle_name)
        #     except: continue
        #     try: elem.click()
        #     except: browser.quit(); logging.warn('google+ post handle error 35'); return 0
        try: elem = browser.find_element_by_xpath('//div[text()="Share"]')
        except: browser.quit(); logging.warn('google+ post handle error 36'); return 0
        try: elem.click()
        except: browser.quit(); logging.warn('google+ post handle error 37'); return 0
        sleep(2)
        try: elem = browser.find_element_by_xpath('//div[contains(text(), "Your post has been shared.")]')
        except: browser.quit(); logging.warn('google+ post handle error 38'); return 0
        if not elem.is_displayed(): browser.quit(); logging.warn('google+ post handle error 39'); return 0
        browser.quit()
        return 1

