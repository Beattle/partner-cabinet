# language: ru
Функционал: Партнёр. Запрос на сброс пароля


  Сценарий: Ошибка валидации. Почта не передана
    Пусть существует партнер "Иван"
    Если пришел запрос POST /partners/reset-password-request, с телом запроса:
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
            }
        ]
    }
    """

  Сценарий: Ошибка валидации. Некорректная почта
    Пусть существует партнер "Иван"
    Если пришел запрос POST /partners/reset-password-request, с телом запроса:
    """
    {
      "email": "123213123"
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
                "path": "email",
                "message": "This value is not a valid email address."
            }
        ]
    }
    """

  Сценарий: Пользователь не найден
    Если пришел запрос POST /partners/reset-password-request, с телом запроса:
    """
    {
      "email": "test@test.com"
    }
    """
    То HTTP-код ответа будет 404
    И данные ответа содержат:
    """
    {
        "~type": "NotFoundError",
        "message": "Не найден пользователь с указанным email",
    }
    """

  Сценарий: Успешный запрос
    Пусть существует партнер "Иван" с почтой "test@test.com"
    Если пришел запрос POST /partners/reset-password-request, с телом запроса:
    """
    {
      "email": "test@test.com"
    }
    """
    То HTTP-код ответа будет 200
    И данные ответа содержат:
    """
    {
        "status": true
    }
    """
    И в БД должен быть партнер с данными:
    """
    {
      "passwordResetToken": "{passwordResetToken партнера "Иван"}"
    }
    """




