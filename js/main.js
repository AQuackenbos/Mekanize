window.isMobile = (/iPhone|iPod|iPad|Android|BlackBerry/).test(navigator.userAgent);

//============ Core Setup

var App = Ember.Application.create();

DS.RESTAdapter.reopen({
	namespace: 'api'
});

App.Store = DS.Store.extend({
	revision: 12
});

//============ Models

App.Character = DS.Model.extend({
	name: DS.attr('string'),
	descShort: DS.attr('string'),
	descLong: DS.attr('string'),
	firstSeen: DS.belongsTo('App.Episode'),
	episodes: DS.hasMany('App.Episode'),
	locations: DS.hasMany('App.Location'),
	organizations: DS.hasMany('App.Organization'),
	mainImage: DS.attr('string'),
	previewImage: DS.attr('string'),
	images: DS.attr('string'),
	backgroundImages: DS.attr('string'),
});

App.Episode = DS.Model.extend({
	title: DS.attr('string'),
	season: DS.attr('number'),
	episode: DS.attr('number'),
	descShort: DS.attr('string'),
	descLong: DS.attr('string'),
	images: DS.attr('string'),
	backgroundImages: DS.attr('string'),
	mainImage: DS.attr('string'),
	previewImage: DS.attr('string'),
	firstSights: DS.hasMany('App.Character'),
	characters: DS.hasMany('App.Character'),
	locations: DS.hasMany('App.Location')
});

App.Organization = DS.Model.extend({
	title: DS.attr('string'),
	firstMention: DS.belongsTo('App.Episode'),
	members: DS.hasMany('App.Character'),
	mainImage: DS.attr('string'),
	previewImage: DS.attr('string'),
	images: DS.attr('string'),
	backgroundImages: DS.attr('string')
});

App.Location = DS.Model.extend({
	name: DS.attr('string'),
	firstMention: DS.belongsTo('App.Episode'),
	visitors: DS.hasMany('App.Character'),
	homeOf: DS.hasMany('App.Character'),
	mainImage: DS.attr('string'),
	previewImage: DS.attr('string'),
	images: DS.attr('string'),
	backgroundImages: DS.attr('string')
});

//============ Objects

App.Season = Ember.Object.extend();

//============ Routes

App.CharactersRoute = Ember.Route.extend({
	model: function() {
		return App.Character.find();
	}
});

App.EpisodesRoute = Ember.Route.extend({
	setupController: function(controller,model){
		controller.set('content',model);
		var MAX_SEASON = 5;
		var seasons = [];
		
		for(var s = 0; s <= MAX_SEASON; s++)
		{
			var eList = App.Episode.find({season:s});
			var title = '';
			if(s == 0)
			{
				title = "Specials";
			}
			else
			{
				title = "Season "+s;
			}
			var season = App.Season.create({
				title: title,
				episodes: eList
			});
			
			seasons.push(season);
		}
		
		controller.set('seasons',seasons);
	},
	model: function() {
		return App.Episode.find();
	}
});

App.EpisodeRoute = Ember.Route.extend({
	setupController: function(controller,model){	
		controller.set('content',model);
		window.ct = model;
		var titleString = model.get('mainImage');
		if(!titleString)
		{
			titleString = 'defaultTitle.png';
		}
		
		controller.set('titleImage',"/img/"+titleString);
		
		if(!model.get('descLong')) return;
		
		var descHtml = '';
		
		model.get('descLong').split("\n").forEach(function(item){
			if(item.trim() == '') return true; //continue
			descHtml += '<p>'+item+'</p>';
		});
		
		controller.set('description',descHtml);
	},
	contextDidChange: function() {
		App.resetBackground(false);
		var bgString = this.context.get('backgroundImages');
		var wasDefault = App.defaultBackground;
		App.defaultBackground = false;
		
		if(!bgString)
		{
			bgString = 'defaultBackground.jpg';
			App.defaultBackground = true;
		}
		
		if(!(App.defaultBackground && wasDefault))
		{
			var bgImages = bgString.split(',').map(function(i){ return '/img/'+i;});
			$.backstretch(bgImages, {duration: 3000, fade: 750});
		}
	}
});

//============ Controllers

//============ Full Views

App.IndexView = Ember.View.extend({
	didInsertElement: function(){
		App.resetBackground(true);
	}
});

App.CharactersView = Ember.View.extend({
	didInsertElement: function(){
		App.resetBackground(true);
	}
});

App.EpisodesView = Ember.View.extend({
	didInsertElement: function(){
        $("#episodes-nav-list").accordion({
			header:'h3',
			heightStyle:'content',
			collapsible: true
		});
		
		App.resetBackground(true);
	}
});

App.LocationsView = Ember.View.extend({
	didInsertElement: function(){
		App.resetBackground(true);
	}
});

App.OrganizationsView = Ember.View.extend({
	didInsertElement: function(){
		App.resetBackground(true);
	}
});

App.EpisodeView = Ember.View.extend({
	didInsertElement: function(){
		$("#episodes-nav-list").accordion( "option", "active", this.get('controller').get('content').get('season'));
		App.resetBackground(false);
		var bgString = this.get('controller').get('content').get('backgroundImages');
		var wasDefault = App.defaultBackground;
		App.defaultBackground = false;
		
		if(!bgString)
		{
			App.defaultBackground = true;
		}
		
		if(!(App.defaultBackground && wasDefault))
		{
			var bgImages = bgString.split(',').map(function(i){ return '/img/'+i;});			
			$.backstretch(bgImages, {duration: 3000, fade: 750});
		}
	}
});

//============ Element Views

App.NavView = Ember.View.extend({
	didInsertElement: function(){
		var skew = -30;
		this.$().children().each(function(index){
			if(skew == 0) skew += 10;
			$(this).css({
				'transform':'skew('+skew+'deg)',
				'-webkit-transform':'skew('+skew+'deg)',
				'-ms-transform':'skew('+skew+'deg)'
			});
			skew += 15;
		});
	}
});

App.TabView = Ember.View.extend({
	tabButtonPrefix: 'tab-',
	tabContentPrefix: 'tab-content-',
	click: function(e){
		var targetButton = this.$().find('.tab-button');
	
		this.$().parent().find('.tab-button').removeClass('active');
		this.$().parent().parent().find('.tab-content').hide();
		targetButton.addClass('active');
		
		var targetContentId = targetButton[0].id.replace('tab-','tab-content-');
		
		$('#'+targetContentId).show();
	}
});

//============ Mapping and additional

if(navigator.appName != "Microsoft Internet Explorer")
{
	App.Router.reopen({
		location: 'history'
	});
}

App.Router.map(function(){
	this.resource('characters',function(){
		this.resource('character',{path:':character_id'});
	});
	this.resource('episodes',function(){
		this.resource('episode',{path:':episode_id'});
	});
	this.resource('locations',function(){
		this.resource('location',{path:':location_id'});
	});
	this.resource('organizations',function(){
		this.resource('organization',{path:':organization_id'});
	});
});

App.resetBackground = function(fullReset){
	if(App.defaultBackground) return;
	
	$('.backstretch').remove();
	
	if(fullReset)
	{
		$.backstretch('/img/defaultBackground.jpg', {duration: 3000, fade: 750});
		App.defaultBackground = true;
	}
}
App.resetBackground(true);

App.defaultBackground = true;

