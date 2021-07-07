# language: ru
Функционал: Партнёр. Логин

  # На текущий момент в тестах нельзя сформировать POST запрос с пустым телом
  # Все пустые запросы отправляются в виде "[]", что выдаёт ошибку invalid json
  # Соответственно получить сценарий вида
  #         if (empty($request->getContent())) {
  #            throw new BadRequestHttpException(' Не указаны данные для входа');
  #        }
  # пока невозможно

  Сценарий: Ошибка валидации. Неправильное тело запроса
    Если пришел запрос POST /partners/login, с телом запроса:
    """
    {
      "fake": "data"
    }
    """
    То HTTP-код ответа будет 400
    И данные ответа содержат:
    """
    {
        "~type": "MalformedRequestError",
    }
    """
  Сценарий: Ошибка валидации. Отсутствует пароль
    Если пришел запрос POST /partners/login, с телом запроса:
    """
    {
      "email": "data@data.ru"
    }
    """
    То HTTP-код ответа будет 400
    И данные ответа содержат:
    """
    {
        "~type": "MalformedRequestError",
    }
    """

  Сценарий: Ошибка валидации. Переданный пароль не содержит строку
    Если пришел запрос POST /partners/login, с телом запроса:
    """
    {
      "email": "data@data.ru",
      "password":123456
    }
    """
    То HTTP-код ответа будет 400
    И данные ответа содержат:
    """
    {
        "~type": "MalformedRequestError",
        "message": "The key \"password\" must be a string."
    }
    """
  Сценарий: Ошибка валидации. Пользователя не существует
    Если пришел запрос POST /partners/login, с телом запроса:
    """
    {
      "email": "data@data.ru",
      "password":"123456"
    }
    """
    То HTTP-код ответа будет 401
    И данные ответа содержат:
    # Тип ошибки ~type не возвращается потому что в api-exceptions-bundle
    # Нет типа BadCredentialsException
    """
    {
      "error":"Invalid credentials."
    }
    """

  Сценарий: Успешная аутентификация партнера
    Пусть существует партнер "Иван" с почтой "email@example.com" с паролем "123456789"
    Если пришел запрос POST /partners/login, с телом запроса:
    """
    {
      "email": "email@example.com",
      "password": "123456789"
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



