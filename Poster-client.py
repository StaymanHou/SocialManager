from twisted.internet import ssl, reactor
from Lib.TaskHandleThread import TaskHandleThread
from Lib.PosterClient import PosterClientFactory
import logging
import Queue
import threading

LOG_FILE_PATH = 'log_poster_client.txt'

#################
# Setup logging #
#################
logger = logging.getLogger('')
logger.setLevel(logging.DEBUG)
# create file handler which logs warning or higher
fh = logging.FileHandler(LOG_FILE_PATH)
fh.setLevel(logging.WARNING)
# create console handler with a lower log level
ch = logging.StreamHandler()
ch.setLevel(logging.INFO)
# create formatter and add it to the handlers
formatter = logging.Formatter('%(asctime)s - %(levelname)s - %(message)s')
ch.setFormatter(formatter)
fh.setFormatter(formatter)
# add the handlers to logger
logger.addHandler(ch)
logger.addHandler(fh)

queueLock = threading.Lock()
workQueue = Queue.Queue(1)

taskhandlethread = TaskHandleThread(workQueue, queueLock)
taskhandlethread.start()

factory = PosterClientFactory(taskhandlethread, workQueue, queueLock)
reactor.connectSSL('localhost', 8123, factory, ssl.ClientContextFactory())
reactor.run()
