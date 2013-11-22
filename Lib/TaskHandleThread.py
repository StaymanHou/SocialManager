import threading
from time import sleep
import logging

TEMP_IMG_DIR = 'temp/'

class TaskHandleThread (threading.Thread):

	def __init__(self, workQueue, queueLock):
		threading.Thread.__init__(self)
		self.status = 'idel'
		self.workQueue = workQueue
		self.queueLock = queueLock
		self.sessions = {}

	def run(self):
		while 1:
			sleep(10)
			self.check()

	def check(self):
		task = None
		if self.status != 'idel':
			return
		with self.queueLock:
			if self.workQueue.empty():
				return
			else:
				task = self.workQueue.get()
		self.status = 'working'
		result = {'id': task['queueItem']['ID'], 'status': 'succeeded'}
		try:
			self.perform(task)
		except Exception, e:
			result['status'] = 'failed'
			logging.warning('Poster-client: @%s #%s | [FAILED] %s\nError: %s'%(task['Acc']['NAME'], self.module_name, (task['QI']['TITLE'])[:16], str(e)))
		else:
			logging.info('Poster-client: @%s #%s | [POSTED] %s'%(task['Acc']['NAME'], self.module_name, (task['QI']['TITLE'])[:16]))
		with self.queueLock:
			self.workQueue.put(result)
		self.status = 'finish'

	def perform(self, task):
		modname = 'PostHandler.'+task['Mod']['NAME']+'_posterhandler'
		mod = __import__(modname, fromlist=[''])
		handler = mod.handler()
		handler.handle(self.sessions, task, TEMP_IMG_DIR)

