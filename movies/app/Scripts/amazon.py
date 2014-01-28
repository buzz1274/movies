#!/usr/bin/python
# -*- coding: utf-8 -*-
import urllib
import urllib2
import mechanize
import re
from bs4 import BeautifulSoup

class AmazonException(object):
    """
    extends base exception class
    """
    pass


class Amazon(object):
    """
    scrapes amazon website.
    """

    def __init__(self, ):
        pass

    def _get_page_mechanize(self, page):
        """
        retrieves the appropriate movie page from imdb using mechanize
        """
        browser = mechanize.Browser()
        browser.set_handle_robots(False)
        browser.addheaders = [('User-agent',
                               'Mozilla/5.0 (Windows NT 6.1; WOW64) '\
                               'AppleWebKit/537.4 (KHTML, like Gecko) '\
                               'Chrome/22.0.1229.94 Safari/537.4')]
        browser.open(page)
        return BeautifulSoup(browser.response().read(), "html5lib",
                             from_encoding='utf-8')


    def get_secondhandprice(self, asin):
        """
        retrieves secondhand price from amazon marketplace page for
        the supplied asin
        @return decimal
        """
        page = self._get_page_mechanize('http://www.amazon.co.uk/gp/offer-listing/%s' % (asin,))
        price = page.find('span', {'class': 'price'})

        if not price:
            price = False
        else:
            try:
                price = re.sub('[^0-9\.]', '', price.contents[0])
                float(price)
            except ValueError, e:
                price = False

        return price



