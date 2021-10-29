# AdvancedCMS
Библиотека, которая позволит вам манипулировать CAdES или иначе стандартом электронной подписи, представляющий 
собой расширенную версию стандарта электронной подписи CMS (Cryptographic Message Syntax) и разработанный ETSI.

[![Build Status](https://travis-ci.org/Falseclock/AdvancedCMS.svg?branch=master)](https://travis-ci.org/Falseclock/AdvancedCMS)
[![PHP 7 ready](https://php7ready.timesplinter.ch/Falseclock/AdvancedCMS/master/badge.svg)](https://travis-ci.org/Falseclock/AdvancedCMS)
[![Coverage Status](https://coveralls.io/repos/github/Falseclock/AdvancedCMS/badge.svg?branch=master&v=2)](https://coveralls.io/github/Falseclock/AdvancedCMS?branch=master)

[![Latest Stable Version](https://poser.pugx.org/falseclock/advanced-cms/v)](//packagist.org/packages/falseclock/advanced-cms)
[![Total Downloads](https://poser.pugx.org/falseclock/advanced-cms/downloads)](//packagist.org/packages/falseclock/advanced-cms)
[![Latest Unstable Version](https://poser.pugx.org/falseclock/advanced-cms/v/unstable)](//packagist.org/packages/falseclock/advanced-cms)
[![License](https://poser.pugx.org/falseclock/advanced-cms/license)](//packagist.org/packages/falseclock/advanced-cms)

Установка
------------

```bash
composer require falseclock/advanced-cms
```

Предыстория
------------
Когда в Казахстане и России стала активно внедряться Электронная цифровая подпись (ЭЦП), я с большой для себя неожиданностью обнаружил,
что в PHP нет библиотек, позволяющих не только формализовать ASN.1 запись цифровой подписи в объект, но и тем более модифицировать её, путем добавления
OCSP проверок или TSP меток, удалить электронный контент, чтобы оставить только подписи или просто объединить две разные подписи одних и тех же данных.
Отправной точкой стала библиотека [adapik/cms](https://github.com/Adapik/CMS), в которой на тот момент не было детального описания всех возможных полей и данных в
CAdES. Так начался долгий путь чтения RFC и написания [нескольких тысяч строк кода](https://github.com/Adapik/CMS/graphs/contributors) чтобы полностью и 
детально описать рекомендации в виде библиотеки. Но по договоренности с Александром Даниловым, мы решили, что изначальная библиотека будет работать
исключительно в режиме чтения, а все остальное, если я того желаю, могу сделать в виде отдельного пакета. Что, собственно, и было сделано.


## Манипуляторы

* [EncapsulatedContentInfo](#EncapsulatedContentInfo)
* [SignedData](#_SignedData_)
* [OCSPRequest](#OCSPRequest)
* [OCSPResponse](#OCSPResponse)
* [OCSPResponseStatus](#OCSPResponseStatus)
* [PKIStatusInfo](#PKIStatusInfo)
* [Request](#Request)
* [ResponseBytes](#ResponseBytes)
* [RevocationValues](#RevocationValues)
* [Signature](#Signature)
* [SignedDataContent](#SignedDataContent)
* [SignerInfo](#SignerInfo)
* [TBSRequest](#TBSRequest)
* [Template](#Template)
* [TimeStampRequest](#TimeStampRequest)
* [TimeStampResponse](#TimeStampResponse)
* [TimeStampToken](#TimeStampToken)
* [UnsignedAttributes](#UnsignedAttributes)

* * *

# **EncapsulatedContentInfo**
**EncapsulatedContentInfo** — Согласно [RFC5652](https://datatracker.ietf.org/doc/html/rfc5652#section-5.2) - место хранения подписанных данных. Хранить
данные в слепке ЭЦП не обязательное условие, следовательно, их можно удалять или внедрять. 

## **Методы**

### **setEContent**

setEContent — запись содержимого в виде простых последовательностей байт (октетов).

#### Описание

```php
public function setEContent(OctetString $octetString): self
```

В случае, если подписанные данные и сами подписи хранятся раздельно, но необходимо сформировать полный CMS файл, данный метод позволяет
внедрить содержание в подпись. Следует учесть, что метод не проверяет хэш файла и хэш из подписи, так как алгоритм хеширования может не входить
в стандартную поставку openssl.

* * *

### **unSetEContent**

unSetEContent — удаление подписанных данных из ЭЦП.

#### Описание

```php
public function unSetEContent(): self
```

ЭЦП может содержать, а может и не содержать данные, так как цифровая подпись формируется на основе хэша данных. Чтобы можно было по отдельности
хранить данные и подписи, вполне разумно их разделять, особенно, если данные очень большого объема, так как их всегда можно проверить по хэшу, 
который использовался в подписи. 

* * *

# **SignedData**
**SignedData** — объединение двух цифровых подписей в единую.

## **Методы**

### **mergeCMS**

mergeCMS — объединение дайджестов, публичных и отозванных сертификатов, а также самих подписей

#### Описание

```php
public function mergeCMS(SignedData $signedData): SignedData
```

Метод позволяет собрать несколько CMS файлов в единый, если один и тот же файл был подписан разными системами, либо если каждая подпись хранится отдельно.

* * *
