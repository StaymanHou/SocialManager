from basic_posterhandler import *
import time
from time import sleep
import urllib
import requests
import re
import logging
import json
from lxml import etree
from MyQueue import *
from RssPost import *
from Tags import *
from MyDict import STATUS_DICT

def recfindboard(jsonobj):
    board_dict = {}
    if isinstance(jsonobj, list):
        for child in jsonobj:
            board_dict.update(recfindboard(child))
    elif isinstance(jsonobj, dict):
        if 'board_name' in jsonobj and 'board_id' in jsonobj:
            board_dict.update({jsonobj['board_name']: jsonobj['board_id']})
        for child in jsonobj.values():
            board_dict.update(recfindboard(child))
    else: pass
    return board_dict

class handler(basicposterhandler):

    # override
    def auto_mode_handle(self, acc, accset, am):
        if am['CODE']==1:
            return
        elif am['CODE']==2:
            myqueue = MyQueue()
            myqueue.GetPendingFirst(acc['PK'], am['MODULE'])
            if myqueue['PK'] is not None: return
            lastrp = RssPost.GetLatest(acc['PK'], am['MODULE'], require_image=True)
            if lastrp['PK'] is None: return
            myqueue['STATUS'] = STATUS_DICT['Pending']
            myqueue['ACCOUNT'] = acc['PK']
            myqueue['MODULE'] = am['MODULE']
            myqueue['TYPE'] = 1
            myqueue['TITLE'] = lastrp['TITLE']
            myqueue['CONTENT'] = lastrp['DESCRIPTION']
            myqueue['TAG'] = lastrp['TAG']
            myqueue['LINK'] = lastrp['LINK']
            myqueue['IMAGE_FILE'] = lastrp['IMAGE_FILE']
            board_name = ''
            if 'board_name' in accset['OTHER_SETTING']: board_name = accset['OTHER_SETTING']['board_name']
            maptaglist = Tags.GetMapTagList(lastrp['TAG'])
            if maptaglist is not None and len(maptaglist)>0:
                board_name = maptaglist[0]
            myqueue['OTHER_FIELD'] = {'image_link': lastrp['IMAGE_LINK'],'board_name': board_name}
            myqueue['RSS_SOURCE_PK'] = lastrp['PK']
            myqueue.save()
            return
        else:
            pass
        return

    # override
    def post_handle(self, accset, queueitem, imgdir, load_iteration=1):
        s = requests.Session()
        headers = {'User-Agent': 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0'}
        # visit login page
        url = 'https://pinterest.com/login'
        try: r = s.get(url, headers=headers)
        except Exception, e: logging.warn('pinterest post handle no response: %s : %s'%(url, e)); return 0
        if r.status_code!=200: logging.warn('pinterest post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
        m = re.search('{"app_version": ".+?"}', r.text)
        if m is None: logging.warn('pinterest login error: app_version not found.'); return 0
        app_version = m.group(0)[17:-2]
        headers['X-NEW-APP'] = 1
        headers['X-Requested-With'] = 'XMLHttpRequest'
        headers['Pragma'] = 'no-cache'
        headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8'
        headers['X-CSRFToken'] = r.cookies.get_dict()['csrftoken']
        sleep(load_iteration)
        # login
        url = 'https://pinterest.com/resource/UserSessionResource/create/'
        payload = 'data={"options":{"username_or_email":"'+accset['USERNAME']+'","password":"'+accset['PSWD']+'"},"context":{"app_version":"'+app_version+'"}}&source_url=/login/&module_path=App()>LoginPage()>Login()>Button('
        payload = urllib.quote_plus(payload).replace('%28','(').replace('%29',')').replace('%3D','=').replace('%26','&')+'class_name%3Dprimary%2C+text%3DLog+in%2C+type%3Dsubmit%2C+tagName%3Dbutton%2C+size%3Dlarge)'
        headers['Referer'] = 'https://pinterest.com/login'
        try: r = s.post(url, data=payload, headers=headers)
        except Exception, e: logging.warn('pinterest post handle no response: %s : %s'%(url, e)); return 0
        if r.status_code!=200: logging.warn('pinterest post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
        try: logindata = json.loads(r.text)
        except: logging.warn('pinterest post handle can\'t parse json: %s'%url); return 0
        try: username = logindata['resource_response']['data']['username']
        except: logging.warn('pinterest post handle can\'t find username'); return 0
        sleep(load_iteration)
        # retreive board_id from board_name
        url = '?data='+urllib.quote_plus('{"options":{},"module":{"name":"UserBoards","options":{"username":"'+username+'","secret_board_count":0},"append":false,"errorStrategy":2},"context":{"app_version":"'+app_version+'"}}')
        url += '&source_url='+urllib.quote_plus('/'+username+'/boards/')
        url += '&module_path='+urllib.quote_plus('App()>Header()>UserMenu(resource=UserResource(username='+username+'))>Dropdown()>UserDropdown()>List(items=[object Object],[object Object],[object Object],[object Object],[object Object],[object Object],[object Object], tagName=ul)').replace('%28','(').replace('%29',')')
        url += '&_='+str(int(1000*time.time()))
        url = 'http://pinterest.com/resource/NoopResource/get/'+url
        headers['Referer'] = 'http://pinterest.com/'+username+'/boards/'
        try: r = s.get(url, headers=headers)
        except Exception, e: logging.warn('pinterest post handle no response: %s : %s'%(url, e)); return 0
        if r.status_code!=200: logging.warn('pinterest post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
        try: boarddata = json.loads(r.text)
        except: logging.warn('pinterest post handle can\'t parse json: %s'%url); return 0
        board_dict = recfindboard(boarddata)
        if len(board_dict)==0: logging.warn('pinterest can\'t find any board'); return 0
        board_id = board_dict.values()[0]
        if 'board_name' in accset['OTHER_SETTING'] and accset['OTHER_SETTING']['board_name'] in board_dict:
            board_id = board_dict[accset['OTHER_SETTING']['board_name']]
        if 'board_name' in queueitem['OTHER_FIELD'] and queueitem['OTHER_FIELD']['board_name'] in board_dict:
            board_id = board_dict[queueitem['OTHER_FIELD']['board_name']]
        # pin
        description = ''
        if queueitem['TITLE'] is not None and len(queueitem['TITLE'].strip())>0: description += addhashtag(queueitem['TITLE'], queueitem['TAG'], mode = 1)+' '
        # removed according to  the customers request: if queueitem['CONTENT'] is not None and len(queueitem['CONTENT'].strip())>0: description += addhashtag(queueitem['CONTENT'], queueitem['TAG'])+' | '
        description += 'More: '+queueitem['LINK']
        link = queueitem['LINK']
        image_link = queueitem['OTHER_FIELD']['image_link']
        url = 'http://pinterest.com/resource/PinResource/create/'
        urlencodedlink = urllib.quote_plus(link)
        payload = {'data':{'options':{'board_id': board_id.encode('ascii','replace'),
                                      'description': description.replace('\'','%27'),
                                      'link': link,
                                      'image_url': image_link.encode('ascii','replace'),
                                      'method': 'scraped'},
                           'context':{'app_version': app_version.encode('ascii','replace')}},
                   'source_url': '/pin/find/?url='+urlencodedlink,
                   'module_path':'App()>ImagesFeedPage(resource=FindPinImagesResource(url='+link+'))>Grid()>GridItems()>Pinnable()>ShowModalButton(submodule=[object Object], primary_on_hover=true, color=primary, text=Pin it, class_name=repinSmall, tagName=button, show_text=false, has_icon=true, ga_category=pin_create)#Modal(module=PinCreate())'}
        headers['Referer'] = 'http://pinterest.com/pin/find/?url='+urlencodedlink
        try: r = s.post(url, data=urllib.urlencode(payload).replace('%22','%5C%22').replace('%27','%22').replace('%2527','%27'), headers=headers)           
        except Exception, e: logging.warn('pinterest post handle no response: %s : %s'%(url, e)); return 0
        if r.status_code!=200: logging.warn('pinterest post handle unexpected response: %s : %s'%(url, r.status_code)); return 0
        if r.text.endswith('"error": null}}'): return 1
        logging.warn('pinterest post handle unexpected response: %s : %s'%(url, r.text.encode('ascii','replace')))
        return 0

