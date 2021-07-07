# language: ru
Функционал: Партнёр. Регистрация

  Сценарий: Ошибка валидации. Если не переданы параметры
    Если пришел запрос POST /partners/register, с телом запроса:
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
                "path": "email",
                "message": "This value should not be blank."
            },
            {
                "path": "firstname",
                "message": "This value should not be blank."
            },
            {
                "path": "lastname",
                "message": "This value should not be blank."
            },
            {
                "path": "password",
                "message": "This value should not be blank."
            }
        ]
    }
    """

  Сценарий: Ошибка валидации. Некорректный пароль
    Если пришел запрос POST /partners/register, с телом запроса:
    """
    {
      "email": "email@example.com",
      "phone": "+79202126784",
      "firstname": "Евгений",
      "lastname": "Долгов",
      "middlename": "Валерьевич",
      "password": "999"
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

  Сценарий: Ошибка валидации. Переданный email не уникален
    Пусть существует партнер "Иван" с почтой "email@example.com"
    Если пришел запрос POST /partners/register, с телом запроса:
    """
    {
      "email": "email@example.com",
      "phone": "+79202126784",
      "firstname": "Евгений",
      "lastname": "Долгов",
      "middlename": "Валерьевич",
      "password": "99999999"
    }
    """
    То HTTP-код ответа будет 409
    И данные ответа содержат:
    """
    {
        "~type": "DuplicateError",
        "message": "Этот email уже зарегистрирован в системе",
        "data": {
          "~type": "Partner"
        }
    }
    """

  Сценарий: Ошибка валидации. Переданный телефон не уникален
    Пусть существует партнер "Иван" с телефоном +79201118833
    Если пришел запрос POST /partners/register, с телом запроса:
    """
    {
      "email": "email@example.com",
      "phone": "+79201118833",
      "firstname": "Евгений",
      "lastname": "Долгов",
      "middlename": "Валерьевич",
      "password": "99999999"
    }
    """
    То HTTP-код ответа будет 409
    И данные ответа содержат:
    """
    {
        "~type": "DuplicateError",
        "message": "Этот телефон уже зарегистрирован в системе",
        "data": {
          "~type": "Partner"
        }
    }
    """

  Сценарий: Ошибка валидации. Успешная регистрация
    Если пришел запрос POST /partners/register, с телом запроса:
    """
    {
      "email": "email@example.com",
      "phone": "+79202126784",
      "firstname": "Евгений",
      "lastname": "Долгов",
      "middlename": "Валерьевич",
      "password": "999999"
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
      "email": "email@example.com",
      "phone": "+79202126784"
    }
    """

