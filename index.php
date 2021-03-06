<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
		<title>Mekanize</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- <link href="/css/jquery-ui.css" rel="stylesheet" media="screen"> -->
		<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
		<link href="css/main.css" rel="stylesheet">
    </head>
	<body>
		<script type="text/x-handlebars" data-template-name="application">
			<div class="header-nav">
				{{#linkTo "index" id="home-link"}}Mekanize{{/linkTo}}
				<!--
				{{#view App.NavView}}
					{{#linkTo "characters"}}CHARACTERS{{/linkTo}}
					{{#linkTo "episodes"}}EPISODES{{/linkTo}}
					{{#linkTo "locations"}}LOCATIONS{{/linkTo}}
					{{#linkTo "organizations"}}ORGANIZATIONS{{/linkTo}}
				{{/view}}
				-->
			</div>
			<div class="global-container">
				{{outlet}}
			</div>
			<div class="footer">
				{{partial "footer"}}
			</div>
		</script>
		
		<script type="text/x-handlebars" data-template-name="index">
			--INDEX!
		</script>
		
		<script type="text/x-handlebars" data-template-name="characters">
			<div class="sidebar well sidebar-nav">
				<ul class="nav">
				{{#unless model}}
					~loading
				{{/unless}}
				{{#each character in controller}}
					<li>{{#linkTo character character}}{{character.name}}{{/linkTo}}</li>
				{{/each}}
				</ul>
			</div>
			<div class="content">
				{{outlet}}
			</div>
		</script>
		
		<script type="text/x-handlebars" data-template-name="character">
			{{#unless model}}loading{{/unless}}
			{{model.name}}<br /> 
			{{#each episode in model.episodes}}
				Episode: {{#linkTo episode episode}}{{episode.title}}{{/linkTo}}<br />
			{{/each}}
		</script>
		
		<script type="text/x-handlebars" data-template-name="episodes">
			<div class="sidebar well sidebar-nav">
				<div class="nav" id="episodes-nav-list">
				{{#unless model}}
					~loading
				{{/unless}}
				{{#each season in controller.seasons}}
					<h3 class="nav-header">
						{{season.title}}
					</h3>
					<div class="season-list">
						<ul class="nav">
							{{#each episode in season.episodes}}
								{{#if episode.season}}
									<li>{{#linkTo episode episode}}Episode {{episode.episode}}{{/linkTo}}</li>
								{{else}}
									<li>{{#linkTo episode episode}}{{episode.title}}{{/linkTo}}</li>
								{{/if}}
							{{/each}}
						</ul>
					</div>
				{{/each}}
				</div>
			</div>
			<div class="content">
				{{outlet}}
			</div>
		</script>
		
		<script type="text/x-handlebars" data-template-name="episode">
			{{#unless model}}~loading{{/unless}}
			<img {{bindAttr src="titleImage"}} class="title-image"/>
			<div class="tab-area">
				{{#view App.TabView}}<a class="tab-button active" id="tab-description">Summary</a>{{/view}}
				{{#view App.TabView}}<a class="tab-button" id="tab-characters">Characters</a>{{/view}}
				{{#view App.TabView}}<a class="tab-button" id="tab-locations">Locations</a>{{/view}}
				{{#view App.TabView}}<a class="tab-button" id="tab-organizations">Organizations</a>{{/view}}
			</div>
			<div class="tab-clear">&nbsp;</div>
			<div class="tab-content description" id="tab-content-description" style="display: block">
				{{partial "tab-summary"}}
			</div>
			<div class="tab-content" id="tab-content-characters">
				{{partial "tab-characters"}}
			</div>
			<div class="tab-content" id="tab-content-locations">
				{{partial "tab-locations"}}
			</div>
			<div class="tab-content" id="tab-content-organizations">
				{{partial "tab-organizations"}}
			</div>
		</script>
		
		<script type="text/x-handlebars" data-template-name="_tab-summary">
			{{#unless description}}
				No description for this episode.
			{{else}}
				{{{description}}}
			{{/unless}}
		</script>
		
		<script type="text/x-handlebars" data-template-name="_tab-episodes">
			{{#unless model.episodes}}
				No associated episodes.
			{{else}}
			<ul>
			{{#each character in model.episodes}}
				<li>{{#linkTo episode episode}}{{episode.title}}{{/linkTo}}</li>
			{{/each}}
			</ul>
			{{/unless}}
		</script>
		
		<script type="text/x-handlebars" data-template-name="_tab-firstsights">
			{{#unless model.characters}}
				No first sightings.
			{{else}}
			<ul>
			{{#each character in model.firstSights}}
				<li>{{#linkTo character character}}{{character.name}}{{/linkTo}}</li>
			{{/each}}
			</ul>
			{{/unless}}
		</script>
		
		<script type="text/x-handlebars" data-template-name="_tab-characters">
			{{#unless model.characters}}
				No associated characters.
			{{else}}
			<ul>
			{{#each character in model.characters}}
				<li>{{#linkTo character character}}{{character.name}}{{/linkTo}}</li>
			{{/each}}
			</ul>
			{{/unless}}
		</script>
		
		<script type="text/x-handlebars" data-template-name="_tab-locations">
			{{#unless model.locations}}
				No associated locations.
			{{else}}
			<ul>
			{{#each location in model.locations}}
				<li>{{#linkTo location location}}{{location.name}}{{/linkTo}}</li>
			{{/each}}
			</ul>
			{{/unless}}
		</script>
		
		<script type="text/x-handlebars" data-template-name="_tab-organizations">
			{{#unless model.organizations}}
				No associated organizations.
			{{else}}
			<ul>
			{{#each organization in model.organizations}}
				<li>{{#linkTo organization organization}}{{organization.name}}{{/linkTo}}</li>
			{{/each}}
			</ul>
			{{/unless}}
		</script>
		
		<script type="text/x-handlebars" data-template-name="_footer">
			<!-- Copyright notice -->
			Footer junk;;
		</script>

		<!-- jQuery + Extensions -->
		<script src="js/jquery-1.9.1.js"></script>
		<script src="js/jquery-ui.js"></script>
		
		<!-- Bootstrap -->
		<script src="js/bootstrap.js"></script>
		
		<!-- Handlebars and Ember -->
		<script src="js/handlebars.js"></script> 
		<script src="js/ember.js"></script> 
		<script src="js/ember-data.js"></script>
		
		<script src="js/main.js"></script>
	</body>
</html>