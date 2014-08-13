#!/usr/bin/python
import smtplib
import re
from config import Config
from email.mime.text import MIMEText
from movie import Movie

config = Config()
movie = Movie()
current_genres = movie.current_genres()
current_certificates = movie.current_certificates()

movie.update_rating()
movie.update_movies()
movie.update_invalid_movies()

try:
    imdb_ids = []
    duplicate_imdb_ids = []
    files = movie.scan_folders()

    for file in files:
        imdb_id = re.search("\[(tt[0-9]{1,})\]", file)
        if imdb_id and imdb_id.group(1):
            if imdb_id.group(1) in imdb_ids:
                duplicate_imdb_ids.append(imdb_id.group(1))
            else:
                imdb_ids.append(imdb_id.group(1))

    invalid_movies = movie.find_invalid_movies()
    movies_missing_images = movie.find_missing_images()
    new_genres = list(set(movie.current_genres()) - set(current_genres))
    new_certificates = list(set(movie.current_certificates()) -
                            set(current_certificates))

    if (invalid_movies or movies_missing_images or new_genres or
        new_certificates or duplicate_imdb_ids):

        body = ""
        if invalid_movies:
            body = "Movies with Incomplete Data(%d):\n" % (len(invalid_movies))
            for invalid_movie in invalid_movies:
                body += "%s %s %s\n" % (invalid_movie.movie_id,
                                        invalid_movie.imdb_id,
                                        invalid_movie.title)
            body += "\n"
        if movies_missing_images:
            body += "Movies with Missing Image(%d):\n" % (len(movies_missing_images))
            for movies_missing_image in movies_missing_images:
                body += "%s %s %s\n" % (movies_missing_image.movie_id,
                                        movies_missing_image.imdb_id,
                                        movies_missing_image)

        if new_genres:
            print new_genres
            body += "New genres added:\n%s\n" % ("\n".join(new_genres),)

        if new_certificates:
            body += "New certificates added:\n%s\n" % ("\n".join(new_certificates),)

        if duplicate_imdb_ids:
            body += "Duplicate IMDB IDs Found:\n%s\n" % ("\n".join(duplicate_imdb_ids),)

        print body

        message = MIMEText(body.encode('utf-8'), 'plain', 'utf-8')
        message['Subject'] = 'Movie Spidering Issues'
        message['From'] = config.email_address
        message['To'] = config.email_address

        mail = smtplib.SMTP('localhost')
        mail.sendmail(config.email_address, [config.email_address],
                      message.as_string())
        mail.quit()

except Exception, e:
    pass