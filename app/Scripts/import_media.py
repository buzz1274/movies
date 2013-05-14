import csv
from sqlalchemy import (create_engine, Table, MetaData, exc,
                        select)

db = create_engine('%s://%s:%s@%s:%s/%s' %
                         ("postgresql", "movies",
                          "Ri23a", "localhost",
                          5432, "movies"))

movie_table = Table('movie', MetaData(), autoload=True,
                    autoload_with=db)
media_table = Table('media', MetaData(), autoload=True,
                    autoload_with=db)

with open('movie_media_information.csv', 'rb') as csvfile:
    media = csv.reader(csvfile, delimiter=',', quotechar='"')
    for row in media:
        query = select([movie_table.c.movie_id,
                        movie_table.c.title]).\
                where(movie_table.c.title == row[0])

        movie = db.execute(query).fetchone()

        if not movie:
            print "NOT FOUND:", row[0]
        else:
            query = media_table.insert().\
                         values(media_format_id=row[1],
                                media_region_id=row[2],
                                media_storage_id=row[3],
                                amazon_asin=row[4],
                                purchase_price=1.12,
                                current_price=row[5],
                                special_edition=row[6],
                                boxset=False,
                                notes=row[7]).returning(media_table.c.media_id)
            media = db.execute(query)
            if media:
                query = movie_table.update().\
                                    where(movie_table.c.movie_id==movie[0]).\
                                    values(media_id=media.fetchone()[0])
                db.execute(query)