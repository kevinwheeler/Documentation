<p>Follow these steps to implement the protective collar strategy:</p>

<ol>
    <li>In the <code class="csharp">Initialize</code><code class="python">initialize</code> method, set the start date, set the end date, <a href='/docs/v2/writing-algorithms/securities/asset-classes/us-equity/requesting-data#02-Create-Subscriptions'>subscribe to the underlying Equity</a>, and create an <a href="/docs/v2/writing-algorithms/universes/equity-options">Option universe</a>.</li>
    <div class="section-example-container">
        <pre class="csharp">private Symbol _equitySymbol, _optionSymbol;

public override void Initialize()
{
    SetStartDate(2017, 4, 1);
    SetEndDate(2017, 4, 30);
    SetCash(100000);
    UniverseSettings.Asynchronous = true;
    _equitySymbol = AddEquity("GOOG").Symbol;
    var option = AddOption("GOOG", Resolution.Minute);
    _optionSymbol = option.Symbol;
    option.SetFilter(-10, 10, 0, 30);
}</pre>
        <pre class="python">def Initialize(self) -&gt; None:
    self.SetStartDate(2017, 4, 1)
    self.SetEndDate(2017, 4, 30)
    self.SetCash(100000)
    self.UniverseSettings.Asynchronous = True
    self.equity_symbol = self.AddEquity("GOOG").Symbol
    option = self.AddOption("GOOG", Resolution.Minute)
    self.option_symbol = option.Symbol
    option.SetFilter(-10, 10, 0, 30)<br></pre>
    </div>

    <li>In the <code class="csharp">OnData</code><code class="python">on_data</code> method, select the Option expiry and strikes.</li>
    <p>Note that the call strike must be greater than the put strike.</p>
    <div class="section-example-container">
        <pre class="csharp">public override void OnData(Slice slice)
{
    if (Portfolio.Invested) return;

    // Get the OptionChain
    var chain = slice.OptionChains.get(_optionSymbol, null);
    if (chain == null || chain.Count() == 0) return;

    // Select an expiry date
    var expiry = chain.OrderBy(x =&gt; x.Expiry).Last().Expiry;

    // Select the call and put contracts that expire on the selected date
    var calls = chain.Where(x =&gt; x.Expiry == expiry &amp;&amp; x.Right == OptionRight.Call);
    var puts = chain.Where(x =&gt; x.Expiry == expiry &amp;&amp; x.Right == OptionRight.Put);
    if (calls.Count() == 0 || puts.Count() == 0) return;

    // Select the strike prices of the OTM contracts
    var callStrike = calls.OrderBy(x =&gt; x.Strike).Last().Strike;
    var putStrike = puts.OrderBy(x =&gt; x.Strike).First().Strike;<br></pre>
        <pre class="python">def on_data(self, slice: Slice) -&gt; None:
    if self.portfolio.invested: return

    # Get the OptionChain
    chain = slice.option_chains.get(self._symbol, None)
    if not chain: return

    # Select an expiry date
    expiry = sorted(chain, key = lambda x: x.expiry)[-1].expiry

    # Select the call and put contracts that expire on the selected date
    calls = [x for x in chain if x.right == OptionRight.CALL and x.expiry == expiry]
    puts = [x for x in chain if x.right == OptionRight.PUT and x.expiry == expiry]
    if not calls or not puts: return

    # Select the strike prices of the OTM contracts
    call_strike = sorted(calls, key = lambda x: x.strike)[-1].strike
    put_strike = sorted(puts, key = lambda x: x.strike)[0].strike</pre>
    </div>

    <li>In the <code class="csharp">OnData</code><code class="python">on_data</code> method, call the <code>OptionStrategies.CoveredCall</code> method and then submit the order.</li>
    <div class="section-example-container">
        <pre class="csharp">    var protectiveCollar = OptionStrategies.ProtectiveCollar(_symbol, callStrike, putStrike, expiry);
    Buy(protectiveCollar, 1);
}</pre>
        <pre class="python">protective_collar = OptionStrategies.protective_collar(self._symbol, call_strike, put_strike, expiry)
self.buy(protective_collar, 1)</pre>
    </div>
<?php 
$methodNames = array("Buy");
include(DOCS_RESOURCES."/trading-and-orders/option-strategy-extra-args.php"); 
?>
</ol>