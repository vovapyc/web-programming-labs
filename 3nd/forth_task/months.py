import re


def replace_month(text):
    return re.sub(r'(?i)\b(\d{1,2}) ((січ|лют|берез|квіт|трав|черв|лип|серп|верес|жовт|листопад|груд)[А-я]+)', r'\1 січень', text)


if __name__ == '__main__':
    text = '23 грудня було холодно'
    print(replace_month(text))
