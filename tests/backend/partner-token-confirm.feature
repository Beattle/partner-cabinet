# language: ru
Функционал: Партнёр. Подтверждения токена

  Сценарий: Ошибка валидации. Переданный email не уникален
    Пусть существует партнер "Иван" с токеном email "0123456789"
    Если пришел запрос POST /partners/email-confirm, с телом запроса:
    """
    {
      "email_confirm_token": ""
    }
    """
    То HTTP-код ответа будет 404
    И данные ответа содержат:
    """
    {
        "~type": "NotFoundError",
        "message": "Не указан токен для подтверждения email",
    }
    """
  Сценарий: Ошибка валидации. Не найден токен для пользователя
    Пусть существует партнер "Иван" с токеном email "0123456789"
    Если пришел запрос POST /partners/email-confirm, с телом запроса:
    """
    {
      "email_confirm_token": "99999999"
    }
    """
    То HTTP-код ответа будет 404
    И данные ответа содержат:
    """
    {
        "~type": "NotFoundError",
        "message": "Не найден пользователь для данного токена",
    }
    """

  Сценарий: Успешное подтверждение токена
    Пусть существует партнер "Иван" с токеном email "0123456789"
    Если пришел запрос POST /partners/email-confirm, с телом запроса:
    """
    {
      "email_confirm_token": "0123456789"
    }
    """
    То HTTP-код ответа будет 200
    И данные ответа содержат:
    """
    {
        "status": true,
    }
    """
    И в БД должен быть партнер с данными:
    """
    {
      "emailConfirmationToken": null,
      "emailConfirmed": true
    }
    """


