# language: ru
Функционал: Партнёр. Детализация

  Сценарий: Не авторизированный запрос
    Если пришел запрос GET /partners/info
    То HTTP-код ответа будет 401
    И данные ответа содержат:
    """
    {
    }
    """

  Сценарий: Успешный запрос от авторизированного пользователя
    Пусть существует партнер "Иван"
    Если пришел запрос GET /partners/info от авторизованного пользователя "Иван"
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




