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
    count = 0
    for row in media:
        if count > 0:
            query = select([movie_table.c.movie_id,
                            movie_table.c.media_id,
                            movie_table.c.title]).\
                    where(movie_table.c.movie_id == row[0])

            movie = db.execute(query).fetchone()

            if not movie:
                print "NOT FOUND:", row[2]
            elif movie[1]:
                print "MEDIA ASSIGNED:", movie[2]
            else:
                query = select([media_table.c.media_id]).\
                        where(media_table.c.amazon_asin == row[2])
                media_id = db.execute(query).fetchone()

                if media_id:
                    media_id = media_id[0]

                if not media_id:
                    query = media_table.insert().\
                                 values(media_format_id=1,
                                        media_region_id=2,
                                        media_storage_id=row[1],
                                        amazon_asin=row[2],
                                        purchase_price=1.12,
                                        current_price=0.01,
                                        special_edition=row[3],
                                        boxset=row[4],
                                        notes=row[5]).returning(media_table.c.media_id)
                    media_id = db.execute(query).fetchone()[0]

                if media_id:
                    query = movie_table.update().\
                                        where(movie_table.c.movie_id==movie[0]).\
                                        values(media_id=media_id)
                    db.execute(query)

        count += 1