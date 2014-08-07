from bs4 import BeautifulSoup
from config import Config
from sqlalchemy import *
import mechanize
import re
import os
import sys
import subprocess

CLEAN_TITLE_REGEX = ':|/'
MOVIES_TO_POPULATE = 5
config = Config()

if os.listdir(config.path):
    print "movies directory(%s) must be empty" % (config.path,)
    sys.exit()

movies = config.db.execute(select([config.movie_table.c.movie_id,
                                   config.movie_table.c.imdb_id,
                                   config.movie_table.c.title])).\
                           fetchall()

if movies:
    for movie in movies:
        try:
            file = '%s/%s[%s].mkv' % (config.path,
                                      re.sub(CLEAN_TITLE_REGEX, '', movie['title']),
                                      movie['imdb_id'],)
            if not os.path.isfile(file):
                open(file, 'a').close()
        except UnicodeEncodeError:
            pass

else:
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
    tags = page.find('tbody', {'class': 'lister-list'}).\
                findAll('td', {'class': 'titleColumn'})

    if tags:
        for tag in tags:
            link = tag.find('a')
            if movies_found < MOVIES_TO_POPULATE and link:
                href = re.match('\/title\/(.*?)\/', link['href'])
                movies_found += 1
                if href:
                    file = '%s/%s[%s].mkv' % \
                            (config.path,
                             re.sub(CLEAN_TITLE_REGEX, '', link.contents[0]),
                             href.group(1),)

                    if not os.path.isfile(file):
                        open(file, 'a').close()

    subprocess.call(["python", os.getcwd()+"/update_movies.py"])


