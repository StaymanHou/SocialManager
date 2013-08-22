from time import sleep
from datetime import datetime, timedelta
import logging
from Lib.MainConf import *
from Lib.Account import *
from Lib.RssPost import *
import feedparser
import socket

debug_mode = True

timeout = 10
socket.setdefaulttimeout(timeout)

logging.basicConfig(format='%(asctime)s %(levelname)-8s %(message)s',filename='log.txt',datefmt='%m/%d/%Y %I:%M:%S %p',level=logging.WARNING)
console = logging.StreamHandler()
console.setLevel(logging.WARNING)
formatter = logging.Formatter('%(asctime)s %(levelname)-8s %(message)s')
console.setFormatter(formatter)
logging.getLogger('').addHandler(console)

print 'Starting RssPuller'

while 1:
    Config = MainConf.Get()
    AccLst = Account.GetActiveList()
    RssPost.Clear(Config['IMAGE_FILE_DIR'], Config['CACHING_TIME'])
    for Acc in AccLst:
        print 'Puller: Start Checking @%s'%Acc['NAME']
        if Acc['LAST_UPDATE']==None: Acc['LAST_UPDATE'] = datetime.now() - timedelta(days=Config['CACHING_TIME'])
        try: d = feedparser.parse(Acc['RSS_URL'])
        except:
            logging.warning('Can\'t get the following feed: '+Acc['RSS_URL'])
            continue
        modname = 'Lib.'+Acc['NAME']+'_rsshandler'
        try:
            mod = __import__(modname, fromlist=[''])
            handler = mod.myrsshand()
        except:
            modname = 'Lib.basic_rsshandler'
            mod = __import__(modname, fromlist=[''])
            handler = mod.basicrsshand()
        last_update = handler.handle(d, Acc, Config['IMAGE_FILE_DIR'])
        Account.SetLastUpdate(Acc['PK'], last_update)
        print 'Puller: End Checking @%s'%Acc['NAME']
    sleep(Config['PULLER_ITERATION'])

print 'Exiting RssPuller'

