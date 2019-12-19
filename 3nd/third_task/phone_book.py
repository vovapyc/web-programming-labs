import re
import requests


PHONE_BOOK_URL = 'https://pnu.edu.ua/%D1%82%D0%B5%D0%BB%D0%B5%D1%84%D0%BE%D0%BD%D0%BD%D0%B8%D0%B9-%D0%B4%D0%BE%D0%B2%D1%96%D0%B4%D0%BD%D0%B8%D0%BA/#t23'


class PhoneBook:
    def __init__(self, phone_book_url):
        self.html = requests.get(phone_book_url).text

    def _get_mif_facultaty(self):
        return re.search(r'(ФАКУЛЬТЕТ МАТЕМАТИКИ ТА ІНФОРМАТИКИ[\S\s]*?</table)', self.html)[0]

    def get_all_phones(self, html=None):
        if not html:
            html = self.html

        for phone in re.findall(r'<tr>\s+<td class=\"[np]\">.+\s+(\w+).+\s+(\w+ \w+).+\s+<td class=\"[np]\">(\d{2}-\d{2}-\d{2})', html):
            yield {
                'Name': f'{phone[0].capitalize()} {phone[1]}',
                'Phone': phone[2],
            }

    def get_all_mif_phones(self):
        return self.get_all_phones(self._get_mif_facultaty())


if __name__ == '__main__':
    pb = PhoneBook(PHONE_BOOK_URL)
    print(*pb.get_all_mif_phones())
