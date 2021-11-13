<style>
  .innerPost
  {
	  padding: 15px;
	  background: #f5f5f5;
	  border: 1px solid #CCC;
  }

  .innerPost > .inner > figure
  {
		width: 23%;
		float: left;
		margin-top: 5px
  }

  .innerPost > .inner > b
  {
		font-size: 1.5em;
  }

 .innerPost > .inner > .innerStory
  {
		font-size: 0.95em;
		line-height: 20px;
		margin: 0 0 0 26%;
  }
</style>

<div class="innerPost clearfix">
	<h4>Материалы по теме</h4>
    <div class="inner">
		<figure>
			<img src="#{image}" loading="lazy" alt="" />
		</figure>

		<div class="innerStory">
		<a href="#{link}">
		    <b>#{title}</b>
			<p>#{short}</p>
		</a>
		</div>
	</div>
</div>
