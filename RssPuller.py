from time import sleep
from datetime import datetime, timedelta
import logging
from Lib.MainConf import *
from Lib.Account import *
import feedparser
import socket
import os
from Lib.MyFunction import domainFromUrl

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
    for Acc in AccLst:
        print 'Puller: Start Checking @%s'%Acc['NAME']
        if Acc['LAST_UPDATE']==None:
            Acc['LAST_UPDATE'] = datetime.now() - timedelta(days=7)
        rss_urls = [ rss_url.strip() for rss_url in Acc['RSS_URL'].split(',') ]
        for rss_url in rss_urls:
            try: d = feedparser.parse(rss_url)
            except:
                logging.warning('Can\'t get the following feed: '+Acc['RSS_URL'])
                continue
            domain = domainFromUrl(rss_url)
            modname = 'Lib.RssHandler.basic_rsshandler'
            for i in xrange(len(domain.split('.'))):
                subdomain = '_'.join(domain.split('.')[i:])
                if os.path.exists('Lib/RssHandler/'+subdomain+'_rsshandler.py'):
                    modname = 'Lib.RssHandler.'+subdomain+'_rsshandler'
                    break
            try:
                mod = __import__(modname, fromlist=[''])
                handler = mod.myrsshand()
            except:
                logging.warning('Can\'t load mod or handle it: '+modname)
            handler.handle(d, Acc, Config['IMAGE_FILE_DIR'])
        last_update = datetime.now()
        Account.SetLastUpdate(Acc['PK'], last_update)
        print 'Puller: End Checking @%s'%Acc['NAME']
    sleep(Config['PULLER_ITERATION'])

print 'Exiting RssPuller'

