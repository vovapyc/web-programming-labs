import re


WORDS = [
    'білий',
    'чорний',
    'пес',
    'кіт',
]


def is_combination_found(text):
    """
    Return `True` if word's combination was found
    """
    # Generate regex expression for find all words with unlimited space between words
    regex_string = '.*'.join(WORDS)

    return bool(len(re.findall(fr'.*{regex_string}', text)))


def count_of_words(text):
    """
    Return json with count of words in text
    """
    result = {}

    for word in WORDS:
        result[word] = len(re.findall(word, text))

    return result


if __name__ == '__main__':
    text = 'білий, чорний, білий, білий, пес, кіт, кіт, щось інше, кіт, кіт, чорний'
    print(is_combination_found(text))
    print(count_of_words(text))
