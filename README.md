#Modul pro Magento

##Modul pro Magento, vyvíjený panem [Adamem Glumbíkem]("http://aglumbik.cz")
Pan Adam Glumbík vytvořil modul pro Magento, který nabízí zdarma na svých stránkách.
* Modul je kompatibilní i se standardním Magento instalátorem modulů<br> a je otestován pro všechny verze od Magento CE 1.4.
* Návod a popis funkcí naleznete [zde](http://www.zasilkovna.cz/soubory/aglumbik_zasilkovna.pdf")
* Pro stažení modulu navštivte prosím web [aglumbik.cz/magento-moduly]("http://aglumbik.cz/magento-moduly")

###S případnými dotazy ohledně alternativního modulu se obracejte na pana [Glumbíka](mailto:glumbik@aglumbik.cz) tento modul není v režii zásilkovny a tudíž pro něj nemůžeme poskytovat technickou podporu.

##Modul vyvíjený zásilkovnou
Můžete použít i modul vyvinutý zásilkovnou, vzhledem k existenci modulu od pana Glumbíka ale **nebude modul nadále udržován**, doporučujeme tedy použít modul zmíněný výše
###Instalace
1. Nejprve nainstalujte toto rozšíření z vašeho eshopu **přes magento-connect** - [http://connect20.magentocommerce.com/community/Magemaven_OrderComment](http://connect20.magentocommerce.com/community/Magemaven_OrderComment)které přidá na konec checkoutu textové pole pro poznámku, do které se automaticky vloží jméno a číslo pobočky. Vložená pobočka poté bude zozbrazena v přehledu objednávek v administraci
2. Do kořenového adresáře nakopírujte obsah [tohoto archivu »](http://www.zasilkovna.cz/soubory/magento-module.zip). Obsahuje 4 dopravní metody zásilkovna (2 cz, 2 sk - jeden se tedy může použít jako doprava s dobírkou a druhý bez)
3. Pokud používáte checkout-onepage (standartní instalace magenta), <strong>na konec</strong> zmíněných souboru vložte přiložený javascript kod:
**app\design\frontend\base\default\template\checkout\onepage.phtml**
		
<code>
<!-- ZASILKOVNA START -->
<script src="http://www.zasilkovna.cz/api/41494564a70d6de6/branch.js?callback=PacketeryLoaded"></script>
<script type="text/javascript">
	var jQ;
	function PacketeryLoaded (){
		jQ = window.packetery.jQuery;
	}
</script>
<!-- ZASILKOVNA END -->
</code>
	
		<strong>app\design\frontend\base\default\template\checkout\onepage\shipping_method\available.phtml</strong><br>
		<textarea onfocus="this.select();" onclick="this.select();" onkeyup="this.select();" readonly="" id="taCode" style="width: 100%; height: 100px">
<!-- ZASILKOVNA START -->
<script type="text/javascript">
	var api = window.packetery;

	jQ("input[name='shipping_method']:radio").each(function(){
		li = jQ(this).parent("li");
		label = jQ(li).find("label");
		methodName = jQ(this).val();
		if(methodName.indexOf("zasilkovna")>=0){
			country  = methodName.slice(-2);
			jQ(li).append("<br>");
			zasBox = document.createElement("div");
			jQ(zasBox).addClass("zas-box");
			jQ(zasBox).css("display","none");
			jQ(zasBox).append("<p style='color:red; font-weight:bold;display:none;' class='select-branch-msg'>Vyberte pobo&amp;ccaron;ku</p>");
			jQ(zasBox).append("<div class='packetery-branch-list list-type=1 country=" + country + "'>Na&amp;ccaron;&iacute;t&amp;aacute;m seznam pobo&amp;ccaron;ek</div>");
			jQ(li).append(zasBox);
			div = jQ(li).find(".packetery-branch-list")[0];
			api.initialize(api.jQuery(div));
			div.packetery.on("branch-change",function(){branchSelect(this)});
			jQ(this).click(function(){
				radioSelect(this);
			});
		}else{
			jQ(this).click(function(){
				deactiveSelect();
			});
		}

	});

	radioSelect(jQ("input[name='shipping_method']:radio")[0]);//select first

	function branchSelect(div){

		showMessage(div);
	}
	function showMessage(div){
		zas_box = jQ(div).parent("div.zas-box");
		if(div.packetery.option("selected-id")>0){
			jQ(zas_box).find("p.select-branch-msg").css("display","none");
		}else{
			jQ(zas_box).find("p.select-branch-msg").css("display","block");
		}
	}
	function radioSelect(radio){
		jQ(radio).attr("checked",true);
		div = jQ(radio).parent("li").find(".packetery-branch-list")[0];
		activateSelect(div);
	}
	function activateSelect(div){
		jQ(".zas-box").each(function(){
			jQ(this).css("display","none");
		});
		zas_box = jQ(div).parent("div.zas-box");
		jQ(zas_box).css("display","block");
		showMessage(div);
	}
	function deactiveSelect(){
		jQ(".zas-box").each(function(){
			jQ(this).css("display","none");
		});
	}

</script>
<!-- ZASILKOVNA END -->
		</textarea>
		<strong>app\design\frontend\base\default\template\checkout\onepage\review\info.phtml</strong><br>
		<textarea onfocus="this.select();" onclick="this.select();" onkeyup="this.select();" readonly="" id="taCode" style="width: 100%; height: 100px">
<!-- ZASILKOVNA START -->
<div class="connectDiv packetery-branch-list list-type=6 connect-field=#ordercomment-comment" style="border: 1px dotted black;"></div>

<script type="text/javascript">
        var api = window.packetery;
        api.jQuery(".packetery-branch-list.connectDiv").each(function(){
            api.initialize(api.jQuery(this));

        });

</script>
<!-- ZASILKOVNA END -->
		</textarea>
	</li>

	<li>
	V checkoutu by se nyní zobrazí dopravní metody zásilkovny s výběrovým boxem poboček a v posledním kroku se vloží jméno a číslo pobočky do poznámky. Po uložení bude pobočka viditelná v administraci v přehledu objednávek.
	</li>

</ol>
<h2>Informace o modulu</h2>
<p>Podporované verze Magento:</p>
<ul>
	<li>1.7.x</li>
	<li>Při problému s použitím v jiné verzi nebo s nestandartním košíkem nás kontaktujte na adrese <a href="mailto:technicka.podpora@zasilkovna.cz">technicka.podpora@zasilkovna.cz</a></li>
</ul>
<p>Poskytované funkce:</p>
<ul>
	<li>Instalace typu dopravního modulu Zásilkovna
		<ul>
			<li>možnost rozlišení ceny dle cílové země</li>
			<li>vybraná pobočka se zobrazuje v detailu objednávky v administrátorské (back-office) sekci</li>
		</ul>
	
