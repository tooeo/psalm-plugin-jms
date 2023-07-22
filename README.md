# Psalm plugin for checking JMS annotation

Plugin for [Psalm](https://github.com/vimeo/psalm) wich check [JMS serializer](https://github.com/schmittjoh/serializer) annotation&

When you use JMS serializer you need add annotation for properties. And some times you can user classes as a type for properties.

**For example:**
```
class SomeTestFile
{
    /**
     * @JMS\Type('\Tooeo\PsalmPluginJms\Tests\Fixtures\JmsDto');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $good;
    /**
     * @JMS\Type('array<JmsDto>');
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public string $goodArray;
}
```
And you can make a mistake by typing the class name. 

So if you don't have enough tests, you can get problems on production. 
This plugin helps avoid that behavior and prevent creating code with unexist classes in annotations.


**Installation**
```
composer require --dev tooeo/psalm-plugin-jms
vendor/bin/psalm-plugin enable tooeo/psalm-plugin-jms
```
