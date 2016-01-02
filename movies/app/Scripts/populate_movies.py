from bs4 import BeautifulSoup
from config import Config
from sqlalchemy import exc, select
import mechanize
import re
import os
import subprocess

CLEAN_TITLE_REGEX = ':|/'
MOVIES_TO_POPULATE = 25
config = Config()

movies = config.db.execute(select([config.movie_table.c.movie_id,
                                   config.movie_table.c.imdb_id,
                                   config.movie_table.c.title])).\
                           fetchall()

if not movies or len(movies) < MOVIES_TO_POPULATE:
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
                href = re.match('/title/(.*?)/', link['href'])
                movies_found += 1
                if href:
                    query = config.movie_table.insert(). \
                        values(imdb_id=href.group(1),
                               provider_id=1,
                               title=re.sub(CLEAN_TITLE_REGEX, '',
                                            link.contents[0]))
                    try:
                        config.db.execute(query)
                    except exc.IntegrityError:
                        pass

    subprocess.call(["python", os.path.dirname(os.path.realpath(__file__))+
                               "/update_movies.py"])


