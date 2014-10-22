# What is this?!

Hopefully if programmed right, everything should be self explanatory *#phpSpecTalk*

## Run

The following command will install dependencies and run the tests (```behat``` + ```phpspec```):

```
$ composer update && composer test
```

There is also an executable:
```
$ ./cli offers -h
```

### Example

```
$ ./cli offers ./order-xml.xml -o o1 -o2
```

*Note:* offers are registered internally against a code. Makes sense from a technical/business perspective:

Here are the available offers:

```
o1 => "3 for the price of 2"
o2 => "Buy Shampoo & get Conditioner for 50% off"
```
