from time import sleep
from PyUserInput.pykeyboard import PyKeyboard
from basic_posterhandler import *
from selenium import webdriver
import pickle
from ..MyQueue import *
from ..RssPost import *
from ..Tags import *
from ..systemHelper import specialize_path

class handler(basicposterhandler):

	def __init__(self):
		super(handler, self).__init__()
		self.module_name = 'gplus'

	# override
	def without_session(self, load_iteration=1):
		self.browser = webdriver.Firefox()
		try:
			self.inner_without_session(load_iteration)
		except Exception, e:
			raise e
		finally:
			self.browser.quit()

	# override
	def with_session(self, load_iteration=1):
		self.browser = webdriver.Firefox()
		try:
			self.inner_with_session(load_iteration)
		except Exception, e:
			raise e
		finally:
			self.browser.quit()

	def inner_without_session(self, load_iteration=1):
		# log into page
		self.browser.get('https://accounts.google.com/ServiceLogin?hl=en&continue=https://plus.google.com'+self.AccSet['OTHER_SETTING']['page_path'])
		elem = self.browser.find_element_by_id('Email')
		elem.send_keys(self.AccSet['USERNAME'])
		elem = self.browser.find_element_by_id('Passwd')
		elem.send_keys(self.AccSet['PSWD'])
		elem = self.browser.find_element_by_id('signIn')
		elem.click()
		sleep(10)
		self.session = pickle.dumps([ cookie for cookie in self.browser.get_cookies() if cookie['domain'] == '.google.com'])
		self.post(load_iteration)

	def inner_with_session(self, load_iteration=1):
		# load session cookie
		self.browser.get('https://www.google.com')
		sleep(load_iteration)
		cookies = pickle.loads(self.session)
		for cookie in cookies:
			self.browser.add_cookie(cookie)
		self.browser.get('https://plus.google.com'+self.AccSet['OTHER_SETTING']['page_path'])
		sleep(load_iteration)
		self.post(load_iteration)

	def post(self, load_iteration=1):
		# switch to the Share dialog
		elem = self.browser.find_element_by_xpath('//div[text()="Share"]')
		elem.click()
		sleep(10)
		self.browser.switch_to_frame(self.browser.find_element_by_xpath('//iframe[../../div/div[1]/a/div/text()="Share"]'))
		if self.QI['TYPE'] == 1:
			# type 1 link
			elem = self.browser.find_element_by_xpath('//span[@title="Add link"]')
			elem.click()
			elem = self.browser.find_element_by_xpath('//input[../div/text()="Enter or paste a link"]')
			elem.send_keys(self.QI['LINK'])
			elem = self.browser.find_element_by_xpath('//div[text()="Add"]')
			elem.click()
			sleep(10)
			elem = self.browser.find_element_by_xpath('//div[2][../div/text()="Share what\'s new..."]')
			content = addhashtag(self.QI['CONTENT'], self.QI['TAG'], mode = 1)
			elem.send_keys(content.encode('ascii', 'ignore'))
		elif self.QI['TYPE'] == 2 and self.QI['IMAGE_FILE']:
			# type 2 photo
			self.garentee_imgfile()
			elem = self.browser.find_element_by_xpath('//span[@title="Add photos"]')
			elem.click()
			sleep(2)
			elem = self.browser.find_element_by_xpath('//span[text()="Upload from computer"]')
			elem.click()
			sleep(1)
			k = PyKeyboard()
			k.type_string(specialize_path(self.temp_img_dir+self.QI['IMAGE_FILE']), 0.1)
			k.tap_key(k.enter_key, 1, 0.1)
			sleep(10)
			elem = self.browser.find_element_by_xpath('//div[2][../div/text()="Share what\'s new..."]')
			content = addhashtag(self.QI['CONTENT'], self.QI['TAG'], mode = 1) + '\n' + self.QI['LINK']
			elem.send_keys(content.encode('ascii', 'ignore'))
		else:
			raise Exception('wrong type: %d'%self.QI['TYPE'])
		# merge
		elem = self.browser.find_element_by_xpath('//div[text()="Share"]')
		elem.click()
		sleep(5)
		self.session = pickle.dumps([ cookie for cookie in self.browser.get_cookies() if cookie['domain'] == '.google.com'])

