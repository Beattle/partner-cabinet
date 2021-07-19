# language: ru
Функционал: Партнёр. Подтверждение пароля


  Сценарий: Ошибка валидации. Данные не переданы.
    Если пришел запрос POST /partners/reset-password-confirm, с телом запроса:
    """
    {
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
                "message": "This value should not be blank."
            }
        ]
    }
    """

  Сценарий: Ошибка валидации. Не передан токен.
    Если пришел запрос POST /partners/reset-password-confirm, с телом запроса:
    """
    {
      "password": "777777"
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
                "path": "password_reset_token",
                "message": "This value should not be blank."
            }
        ]
    }
    """

  Сценарий: Ошибка валидации. Некорректный пароль
    Если пришел запрос POST /partners/reset-password-confirm, с телом запроса:
    """
    {
      "password": "123",
      "password_reset_token": "9999"
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

  Сценарий: Ошибка валидации. Пользователь не найден
    Если пришел запрос POST /partners/reset-password-confirm, с телом запроса:
    """
    {
      "password": "123456",
      "password_reset_token": 111222
    }
    """
    То HTTP-код ответа будет 403
    И данные ответа содержат:
    """
    {
        "~type": "AccessDeniedError",
        "message": "Не найден пользователь с указанным токеном",
    }
    """

  Сценарий: Успешное подтверждение нового пароля
    Пусть существует партнер "Иван" с паролем "123456" с токеном пароля 0000
    Если пришел запрос POST /partners/reset-password-confirm, с телом запроса:
    """
    {
      "password": "654321",
      "password_reset_token": "0000"
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
      "passwordResetToken": null,
      "passwordHash": "{passwordHash партнера "Иван"}"
    }
    """
    ## Запрашиваем данные об авторизованном пользователе

    Пусть пришел запрос GET /partners/info
    То HTTP-код ответа будет 200

    ## Завершаем сессию пользователя и пробуем авторизоваться под старым паролем

    Если пришел запрос GET /partners/logout
    То HTTP-код ответа будет 200

    Допустим пришел запрос POST /partners/login, с телом запроса:
    """
    {
      "password": "123456",
      "email": "{email партнера "Иван"}"
    }
    """
    Тогда HTTP-код ответа будет 401
    И данные ответа содержат:
    """
    {
      "error": "Invalid credentials."
    }
    """
    # Проверяем новый пароль
    Тогда пришел запрос POST /partners/login, с телом запроса:
    """
    {
      "password": "654321",
      "email": "{email партнера "Иван"}"
    }
    """
    И HTTP-код ответа будет 200




