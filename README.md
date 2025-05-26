# Console-Selection

A fancy selection interface for symfony's console component.

![Sample](docs/sample.gif)

## Requirements 

This project is intended to be run with one of these frameworks:

- symfony/console 5.0+

## Install

```sh
composer require sunkan/console-select
```

### On Symfony

Add the `EddIriarte\Console\Traits\SelectableInputs` trait to your command class

```php
# importing : \EddIriarte\Console\Helpers\SelectionHelper
# passing the input and output interfaces
$this->getHelperSet()->set(
  new SelectionHelper($this->input, $this->output)
);
```

## Checkboxes

Useful when you need several answers from the user.

```php
$selected = $this->select(
  'Select characters that appeared in "Star Wars,  Episode I - The phantom menace"',
  [
    'Ahsoka Tano',
    'Anakin Skywalker',
    'Boba Fett',
    'Chewbacca',
    'Count Dooku',
    'Darth Maul',
    'Darth Vader',
    'Finn',
    'Han Solo',
    'Jabba the Hutt',
    'Jar Jar Binks',
    'Kylo Ren',
    'Lando Calrissian',
    'Luke Skywalker',
    'Mace Windu',
    'Obi-Wan Kenobi',
    'Padmé Amidala',
    'Sheev Palpatine',
    'Poe Dameron',
    'Princess Leia Organa',
    'Qui-Gon Jinn',
    'Rey',
    'Watto',
    'Yoda',
  ]
);
```

## Radio

Useful when you need the user to pick only one anwer from the list.

```php
$selected = $this->select(
  'What is the name of the ancient Jedi master that lives at the swamps of Dagobah',
  [
    'Ahsoka Tano',
    'Anakin Skywalker',
    'Boba Fett',
    'Chewbacca',
    'Count Dooku',
    'Darth Maul',
    'Darth Vader',
    'Finn',
    'Han Solo',
    'Jabba the Hutt',
    'Jar Jar Binks',
    'Kylo Ren',
    'Lando Calrissian',
    'Luke Skywalker',
    'Mace Windu',
    'Obi-Wan Kenobi',
    'Padmé Amidala',
    'Sheev Palpatine',
    'Poe Dameron',
    'Princess Leia Organa',
    'Qui-Gon Jinn',
    'Rey',
    'Watto',
    'Yoda',
  ],
  false // third argument(bool) that allows multiple selections (default: true)
);
```

## Still to do

- Handle user-interruptions, such as `Ctrl+C`

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
