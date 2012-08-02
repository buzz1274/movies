import urllib
from urllib import FancyURLopener
from bs4 import BeautifulSoup

class IMDB(FancyURLopener):
    version = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1.11)'+\
              'Gecko/20071127 Firefox/2.0.0.11'
    
    def __init__(self):
	pass

    def get_movie_details(self, imdb_key):	
	"""
	retrieves the movie details for the supplied imdb key
	"""
	#url = MyOpener()
	#page = url.open('http://www.imdb.com/title/tt0111161/')
	page = open('imdb.html')
	#print page
	#print page.read()
	soup = BeautifulSoup(page)
	print soup.title
	rate = soup.find('a', itemprop='Director')
	#getunicode(rate)
	print rate

	#print soup.prettify()
	#imdb rating
	#top 3 billing cast members
	#runtime
	#certificate
	#film image
	#release date
	#synopsis
	#movie
	#cast
	#movie-cast
	#user
