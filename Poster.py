from time import sleep
from datetime import datetime, timedelta
import logging
from Lib.MainConf import *
from Lib.Account import *
from Lib.AccSetting import *
from Lib.Module import *
from Lib.MyQueue import *
import socket

debug_mode = True

timeout = 60
socket.setdefaulttimeout(timeout)

##### Setup the Logging behavior ######

logging.basicConfig(format='%(asctime)s %(levelname)-8s %(message)s',filename='log.txt',datefmt='%m/%d/%Y %I:%M:%S %p',level=logging.WARNING)
console = logging.StreamHandler()
console.setLevel(logging.WARNING)
formatter = logging.Formatter('%(asctime)s %(levelname)-8s %(message)s')
console.setFormatter(formatter)
logging.getLogger('').addHandler(console)

print 'Starting Poster'

while 1:
    Config = MainConf.Get()
    AccLst = Account.GetActiveList()
    ModLst = Module.GetActiveList()
    MyQueue.Clear(Config['IMAGE_FILE_DIR'], Config['CACHING_TIME'])
    for Acc in AccLst:
        print 'Poster: Start Checking @%s'%Acc['NAME']
        for Mod in ModLst:
            AccSet = AccSetting.GetByAccAndMod(Acc['PK'], Mod['PK'])
            if (AccSet is None) or (len(AccSet)==0):
                logging.warn('Fail to load account setting: %s %s'%(Acc['NAME'], Mod['NAME']))
                continue
            if AccSet['ACTIVE']==False: continue
            modname = 'Lib.'+Mod['NAME']+'_posterhandler'
            try:
                mod = __import__(modname, fromlist=[''])
                handler = mod.handler()
            except Exception, e:
                logging.warn('Fail to load poster module: %s : %s'%(Mod['NAME'], e))
                continue
            handler.handle(Acc, AccSet, Config['IMAGE_FILE_DIR'])
        print 'Poster: End Checking @%s'%Acc['NAME']
    sleep(Config['POSTER_ITERATION'])

print 'Exiting Poster'

