# coding: utf-8
import re


def task1(source):
    for result in re.findall(r'<a href=\".+\"', source):
        yield result[9:-1]


if __name__ == '__main__':
    # First task
    with open('first_task/sources/1.html', 'r') as f:
        print(*task1(f.read()))
