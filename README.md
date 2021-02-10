# [DEPRECATED] - modul již není udržován/no longer actively maintained

[Návod v češtině](https://github.com/Zasilkovna/magento#modul-pro-magento-19)

# Module for Magento 1.9

### Download module

[Current version 4.1](https://github.com/Zasilkovna/magento/archive/v4.2.zip)

### Installation

- unzip the package and copy the *app* and *skin* directories to Magenta
- clean Magento cache 
    - login to the administration
    - in the upper menu select **System** - **Cache Management** 
    - press the button **Flush Magento Cache**
- logout from the administration and login again

### Configuration

Login to the administration, in the upper menu select **System** - **Configuration**. In left menu you can find section *Packeta configuration*.
The configuration is  divided into several parts: *Widget configuration*, *Price rules*, *Cash on delivery*. In each part fill in required informations.
and save the settings pressing the **Save config** button.

#### Widget configuration

- **API key** - you can find it in [client section](https://client.packeta.com/cs/support/) » Client support

#### Price rules

##### Global settings
- **default price** - the shipping price applies if the country-specific default price is not filled
- **Maximum weight** - for orders with a larger weight, the Packeta shipping method will not be offered in the cart
- **free shipping** - if the order price is higher, free shipping

##### Rules - other countries

These rules are not currently applied. They will be removed in the next version of the module.

##### Rules CZ (SK, PL, HU, RO)

Enter prices and shipping pricing rules for each supported country here.

- **default price** - the price will be applied if you do not fill in the pricing rules, or the order weight exceeds the set weighting rules for a particular country
- **free shipping** - if the order price is higher, free shipping
- **price rules** - here you can add more pricing rules for different weight ranges.
    - to create a new rule click on the button * Add Rule *
    - click the * Delete * button to delete the rule
    - fill in the fields * Weight from *, * Weight to * and * Price * for each rule

#### Cash on delivery

Under * Cash on delivery * - * Payment methods *, select the payment methods that will be considered as cash on delivery (for Packeta).
Multiple payment methods can be selected by holding the "Ctrl" button and clicking on the required payment methods

### List of orders

- To enter the order list, select **Sales** - **Zásilkovna-objednávky** in the top menu.
- Export orders to the CSV file:
    - Select the orders you want to export to CSV file.
    - Above the list of orders you will find the ** Actions ** drop-down menu where you select ** CSV export ** and click ** Submit **

### Informations about the module

#### Supported languages:

- czech
- english

#### Supported versions:

- Magento 1.9.x

#### Supported functions:

- Integration of widget for pickup points selections in the eshop cart
- Set different prices for different target countries
- Setting prices according to weighting rules
- Free shipping from the specified price or weight of the order
- Export orders to a csv file that can be imported in [client section](https://client.packeta.com/)

# Modul pro Magento 1.9

### Stažení modulu

[Aktuální verze 4.2](https://github.com/Zasilkovna/magento/archive/v4.2.zip)

### Instalace

- obsah zip balíku rozbalte a adresáře *app* a *skin* zkopírujte do adresáře Magenta
- vyčistěte Magento cache 
    - přihlašte se do adminitrace 
    - v horním menu vyberte **Systém** - **Cache Management** 
    - klikněte na tlačítko **Flush Magento Cache**
- odhlašte se z administrace a znovu se přihlašte

### Konfigurace

Přihlašte se do administrace, v horním menu vyberte **System** - **Configuration**.  V levém menu naleznete sekci *Zásilkovna konfigurace*. 
Konfigurace je rozdělena do několika částí:  *Nastavení widgetu*, *Cenová pravidla*, *Dobírka*.   V každé části je vyplňte požadované údaje 
a nastavení uložte kliknutím na tlačítko **Save Config**

#### Nastavení widgetu 

- **API klíč** - naleznete jej v [klientské sekci](https://client.packeta.com/cs/support/) » Klientská podpora

#### Cenová pravidla

##### Globální nastavení
- **Výchozí cena** - cena za přepravu se použije v případě, že není vyplněna výchozí cena u konkrétní země
- **Maximální váha** - u objednávek s větší hmotnostní nebude v košíku přepravní metoda Zásilkovna nabízena
- **Doprava zdarma** - pokud bude cena objednávky vyšší bude doprava zdarma

##### Pravidla - ostatní země

Tato pravidla se v současné době nepoužívají.  V příští verzi modulu budou odstraněna.

##### Pravidla CZ (SK, PL, HU, RO)

Zde zadejte ceny a pravidla pro výpočet ceny přepravy pro každou podporovanou zemi zvlášť.

- **Výchozí cena** - cena se použije pokud nevyplníte cenová pravidla, nebo hmotnost objednávky přesáhne nastavená váhová pravidla pro konkrétní zemi
- **Doprava zdarma** - pokud bude cena objednávky vyšší bude doprava zdarma
- **Cenová pravidla** - zde můžete přidat více cenových pravidel, pro různá váhová rozmezí.  
    - pro vytvoření nového pravidla klikněte na tlačítko *Přidat pravidlo*
    - pro smazání pravidla klikněte na tlačítko *Delete*
    - u každého pravidla vyplňte pole *Hmotnost od*, *Hmotnost do* a *Cena*

#### Dobírka

V části *Dobírka* - *Platební metody* vyberte platební metody, které budou považovány za platební metody na dobírku (pro Zásikovnu).
Vybrat více platebních metod je možné přidržením tlačítka "Ctrl" a kliknutím na jednotlivé požadované platební metody

### Seznam objednávek

- Pro vstup do seznamu objednávek zvolte položku **Sales** - **Zásilkovna-objednávky** v horním menu.
- Export zásilek do CSV souboru:
    - Označte objednávky které chcete exportovat do CSV souboru.
    - Nad seznamem objednávek naleznete rozbalovací menu **Actions** kde vyberete **CSV export** a kliknete na tlačítko **Submit**

### Informace o modulu

#### Podporované jazyky:

- čeština
- angličtina

#### Podporované verze:

- Magento 1.9.x

#### Poskytované funkce:

- Integrace widgetu v košíku eshopu
- Nastavení různé ceny pro různé cílové země
- Nastavení cen podle váhových pravidel
- Doprava zdarma od zadané ceny nebo hmotnosti objednávky
- Export zásilek do csv souboru, který lze importovat v [klientské sekci](https://client.packeta.com/)
