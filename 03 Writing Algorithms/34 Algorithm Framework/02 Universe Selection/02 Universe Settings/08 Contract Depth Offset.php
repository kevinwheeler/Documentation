<? include(DOCS_RESOURCES."/universes/settings/contract-depth-offset.php"); ?> adjust the algorithm's <code class="csharp">UniverseSettings</code><code class="python">universe_settings</code> before you create the Universe Selection model.</p>

<div class="section-example-container">
	<pre class="csharp">UniverseSettings.ContractDepthOffset = 1;
AddUniverseSelection(
    new FutureUniverseSelectionModel(
        TimeSpan.FromDays(1), 
        _ => new List&lt;Symbol&gt; {{ QuantConnect.Symbol.Create(Futures.Indices.SP500EMini, SecurityType.Future, Market.CME) }}
    )
);</pre>
	<pre class="python">from Selection.FutureUniverseSelectionModel import FutureUniverseSelectionModel

self.universe_settings.contract_depth_offset = 1
self.add_universe_selection(
    FutureUniverseSelectionModel(
        timedelta(1), 
        lambda _: [Symbol.create(Futures.Indices.SP500E_MINI, SecurityType.FUTURE, Market.CME)]
    )
)</pre>
</div>