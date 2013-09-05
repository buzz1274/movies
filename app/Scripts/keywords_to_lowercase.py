from sqlalchemy import *
from sqlalchemy import exc
from config import Config
import re
import sys

config = Config()
keyword_query = select([config.keyword_table.c.keyword_id,
                        config.keyword_table.c.keyword])

keywords = config.db.execute(keyword_query).fetchall()

if keywords:
    for keyword in keywords:
        try:
            if [i for i,c in enumerate(str(keyword[1])) if c.isupper()]:
                keyword_query = select([config.keyword_table.c.keyword_id,
                                        config.keyword_table.c.keyword]).\
                                where(config.keyword_table.c.keyword == keyword[1].lower())
                lowercase = config.db.execute(keyword_query).fetchone()

                if lowercase:
                    movie_uppercase_query = select([config.movie_keyword_table.c.movie_id]).\
                                            where(config.movie_keyword_table.c.keyword_id == keyword[0])

                    movies = config.db.execute(movie_uppercase_query).fetchall()

                    if not movies:
                        query = config.keyword_table.delete().\
                                where(config.keyword_table.c.keyword_id==keyword[0])
                        config.db.execute(query)
                    else:
                        for movie in movies:
                            movie_lowercase_query =\
                                select([config.movie_keyword_table.c.movie_id]).\
                                where((config.movie_keyword_table.c.keyword_id == lowercase[0]).\
                                __and__(config.movie_keyword_table.c.movie_id == movie[0]))
                            if config.db.execute(movie_lowercase_query).fetchall():
                                query = config.movie_keyword_table.delete().\
                                        where((config.movie_keyword_table.c.keyword_id==keyword[0]).\
                                        __and__(config.movie_keyword_table.c.movie_id == movie[0]))
                                config.db.execute(query)
                            else:
                                query = config.movie_keyword_table.insert().\
                                        values(movie_id=movie[0],
                                               keyword_id=lowercase[0],
                                               order=15)
                                config.db.execute(query)

                else:
                    query = config.keyword_table.update().\
                                 where(config.keyword_table.c.keyword_id==keyword[0]).\
                                 values(keyword=keyword[1].lower())

                    config.db.execute(query)

        except Exception, e:
            print e
            pass

