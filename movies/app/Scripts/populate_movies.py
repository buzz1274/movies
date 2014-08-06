from bs4 import BeautifulSoup
from config import Config
import mechanize
import re
import os
import sys
import subprocess

MOVIES_TO_POPULATE = 1
config = Config()

if os.listdir(config.path):
    print "movies directory(%s) must be empty" % (config.path,)
    sys.exit()

#if movies in db loop through and create filenames from db

browser = mechanize.Browser()
browser.set_handle_robots(False)
browser.addheaders = [('User-agent',
                       'Mozilla/5.0 (Windows NT 6.1; WOW64) ' \
                       'AppleWebKit/537.4 (KHTML, like Gecko) ' \
                       'Chrome/22.0.1229.94 Safari/537.4')]
browser.open("http://www.imdb.com/chart/top")
page = BeautifulSoup(browser.response().read(), "html5lib",
                     from_encoding='utf-8')

movies_found = 0
tags = page.find('tbody', {'class': 'lister-list'}).findAll('td', {'class': 'titleColumn'})

if tags:
    for tag in tags:
        link = tag.find('a')
        if movies_found < MOVIES_TO_POPULATE and link:
            href = re.match('\/title\/(.*?)\/', link['href'])
            movies_found += 1
            if href:
                file = '%s/%s[%s].mkv' % (config.path,
                                          re.sub(':', '', link.contents[0]),
                                          href.group(1),)

                if not os.path.isfile(file):
                    open(file, 'a').close()

subprocess.call(["python", os.getcwd()+"/update_movies.py"])


