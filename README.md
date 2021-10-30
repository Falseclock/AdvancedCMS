# AdvancedCMS
A PHP Library that allows you to decode and manipulate CAdES or in other words CMS Advanced Electronic Signatures described in ETSI standart TS 101 733.

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
Когда в Казахстане и России стала активно внедряться Электронная цифровая подпись (ЭЦП), я, с большой для себя неожиданностью, обнаружил,
что в PHP нет библиотек, позволяющих не только формализовать ASN.1 запись цифровой подписи в объект, но и тем более модифицировать её, путем добавления
OCSP проверок или TSP меток, удалить электронный контент, чтобы оставить только подписи или просто объединить две разные подписи одних и тех же данных.
Отправной точкой стала библиотека [adapik/cms](https://github.com/Adapik/CMS), в которой на тот момент не было детального описания всех возможных полей и данных в
CAdES. Так начался долгий путь чтения RFC и написания [нескольких тысяч строк кода](https://github.com/Adapik/CMS/graphs/contributors) чтобы полностью и 
детально описать рекомендации в виде библиотеки. Но, по договоренности с [Александром Даниловым](https://github.com/Adapik), мы решили, что изначальная библиотека будет, как и раньше,
работать исключительно в режиме чтения, а все остальное, если я того желаю, могу сделать в виде отдельного пакета. Что, собственно, и было сделано.

Человеку, не понимающему как работает ЭЦП, как проверяется легитимность и достоверность подписи будет довольно сложно понять суть и смысл этой библиотеки.
Тем не менее я постарался сделать все возможные unit тесты и 100% покрыть код. Как работать с CMS файлами, изменять или создавать новые вы можете на основе тестов
в папке [test](https://github.com/Falseclock/AdvancedCMS/tree/master/tests). В дальнейших планах реализация не только манипуляций с подписями, но и также
формирование ЭЦП в виде CAdES. В этом смысле передача пароля или приватного ключа не всегда оказывается хорошей практикой, но раз уж на PHP до сих пор нет такой
реализации, то почему бы и нет.

## Манипуляторы

* [EncapsulatedContentInfo](#EncapsulatedContentInfo)
* [SignedData](#SignedData)
* [SignedDataContent](#SignedDataContent)
* [SignerInfo](#SignerInfo)
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
**SignedData** — объект, включающий в себя все данные цифровой подписи.

## **Методы**

### **mergeCMS**

mergeCMS — объединение дайджестов, публичных и отозванных сертификатов, а также самих подписей

#### Описание

```php
public function mergeCMS(SignedData $signedData): SignedData
```

Метод позволяет собрать несколько CMS файлов в единый, если один и тот же файл был подписан разными системами, либо если каждая подпись хранится отдельно.

* * *

# **SignedDataContent**
**SignedDataContent** — согласно [RFC5652](https://datatracker.ietf.org/doc/html/rfc5652#section-5.1) это последовательность нескольких элементов, которая включает
дайджест, подписанные данные, сертификаты, отзывы на сертификаты и данные по подписях.

## **Методы**

### **appendDigestAlgorithmIdentifier**

appendDigestAlgorithmIdentifier — добавление в список текущих хэш алгоритмов, дополнительного, используемого в одной из подписей

#### Описание

```php
public function appendDigestAlgorithmIdentifier(AlgorithmIdentifier $algorithmIdentifier): self
```

Неупорядоченный набор дайджестов (хэш алгоритмов) использованных в подписях. При этом, если в CMS находятся две или более подписи, с одним и тем же
алгоритмом хеширования, наборы могут повторяться. Количество последовательностей алгоритмов в данном наборе соответствует количеству подписей.

* * *

### **appendCertificate**

appendCertificate — добавление публичного сертификата как для одной из подписей, так и других вспомогательных.

#### Описание

```php
public function appendCertificate(Certificate $certificate): self
```

Следует учесть, что наличие публичных сертификатов в CMS не является обязательным условием, так как согласно RFC это поле опциональное. Согласно тому же 
[RFC5652](https://datatracker.ietf.org/doc/html/rfc5652#section-10.2.3) в этом наборе могут присутствовать как сертификаты самих подписей, так и промежуточные
или же корневые сертификаты при необходимости.

* * *

### **appendSignerInfo**

appendSignerInfo — добавление подписи в CMS, которая включает алгоритм хеширования, подпись, подписанные и неподписанные атрибуты.

#### Описание

```php
public function appendSignerInfo(Certificate $certificate): self
```

Это один из основных методов, ради которого и затеялась разработка данной библиотеки. Позволяет объединять несколько подписей в единый CMS файл.

* * *

# **SignerInfo**
**SignerInfo** — согласно [RFC5652](https://datatracker.ietf.org/doc/html/rfc5652#section-5.3) в подписи могут содержаться неподписанные данные. Например, 
OCSP проверка или TSP метка. Несмотря на то, что эти данные также могут находиться и в подписанных данных, не все средства подписания реализуют сохранение
дополнительных атрибутов в блоке подписи. Для этого в SignerInfo предусмотрен блок UnsignedAttributes, куда важные данные для проверки легитимности подписи
могут быть добавлены позднее. Более подробно об атрибутах можно найти информацию в [RFC5126](https://datatracker.ietf.org/doc/html/rfc5126).

## **Методы**

### **addUnsignedAttribute**

addUnsignedAttribute — добавление неподписанного атрибута

#### Описание

```php
public function addUnsignedAttribute(UnsignedAttribute $newAttribute): SignerInf
```

Как правило, это OCSP и TSP данные. На самом деле, атрибуты не ограничены только этими проверками и метками. Тем не менее одинаковые типы данных
хранятся в неупорядоченном наборе последовательности, имеющий OID идентификатор, указывающий какой набор данных содержит та или иная последовательность.
Иными словами, например OCSP метки будут храниться в SET поле SEQUENCE, которая имеет OID 1.2.840.113549.1.9.16.2.24 (revocationValues), а TSP метки
будут храниться в наборе последовательности, которая имеет OID 1.2.840.113549.1.9.16.2.14 (timeStampToken)

* * *

### **createUnsignedAttributes**

createUnsignedAttributes — создание набора неподписанных атрибутов

#### Описание

```php
protected function createUnsignedAttributes(): ?UnsignedAttributes
```

Электронная цифровая подпись необязательно должна иметь неподписанные данные, тем не менее, этот набор может быть добавлен в ЭЦП не нарушая целостность её данных.

* * *

### **deleteUnsignedAttributes**

deleteUnsignedAttributes — удаление неподписанных атрибутов

#### Описание

```php
public function deleteUnsignedAttributes(): SignerInfo
```

Так же как и с добавлением дополнительных атрибутов, они могут быть легко удалены, при этом целостность и достоверность подписи не будет нарушена. 

* * *
