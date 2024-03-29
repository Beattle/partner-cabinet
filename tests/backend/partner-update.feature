# language: ru
Функционал: Партнёр. Обновление данных

  Сценарий: Не авторизированный запрос
    Если пришел запрос POST /partners/update
    То HTTP-код ответа будет 401
    И данные ответа содержат:
    """
    {
    }
    """

  Сценарий: Ошибка валидации. Данные не переданы
    Пусть существует партнер "Иван"
    Если пришел запрос POST /partners/update от авторизованного пользователя "Иван", с телом запроса:
    """
    {}
    """
    То HTTP-код ответа будет 400
    И данные ответа содержат:
    """
    {
        "~type": "ValidationError",
        "message": "Некорректны входные данные",
        "errors": [
            {
                "path": "data",
                "message": "Не указаны данные для обновления"
            }
        ]
    }
    """

  Сценарий: Ошибка валидации. Некорректный пароль
    Пусть существует партнер "Иван"
    Если пришел запрос POST /partners/update от авторизованного пользователя "Иван", с телом запроса:
    """
    {
      "password": "777"
    }
    """
    То HTTP-код ответа будет 400
    И данные ответа содержат:
    """
    {
        "~type": "ValidationError",
        "message": "Некорректны входные данные",
        "errors": [
            {
                "path": "password",
                "message": "Пароль должен состоять как минимум из 6 символов."
            }
        ]
    }
    """

  Сценарий: Успешное обнрвление
    Пусть существует партнер "Иван" с паролем "111111"
    Если пришел запрос POST /partners/update от авторизованного пользователя "Иван", с телом запроса:
    """
    {
      "password": "777777",
      "phone": "+79201324456",
      "firstname": "Марина",
      "lastname": "Хэнцис",
      "middlename": "Энн",
    }
    """
    То HTTP-код ответа будет 200
    И данные ответа содержат структуру:
    """
    {
        "~type": "string:regex({Partner})",
        "~id": "string",
        "email": "string",
        "phone": "string",
        "firstname": "string",
        "middlename": "string",
        "lastname": "string"
    }
    """
    И в БД должен быть партнер с данными:
    """
    {
      "phone": "+79201324456",
      "firstname": "Марина",
      "lastname": "Хэнцис",
      "middlename": "Энн",
    }
    """
    ### Пробуем зайти под пользователем с новым паролем
    Пусть пришел запрос POST /partners/login, с телом запроса:
    """
    {
      "email": "{email партнера "Иван"}",
      "password": "777777"
    }
    """
    То HTTP-код ответа будет 200
    И данные ответа содержат структуру:
    """
    {
        "~type": "string:regex({Partner})",
        "~id": "string",
        "email": "string",
        "phone": "string",
        "firstname": "string",
        "middlename": "string",
        "lastname": "string"
    }
    """




