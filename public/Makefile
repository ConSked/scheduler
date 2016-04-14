SHELL = /bin/sh

.SUFFIXES:
.SUFFIXES: .frag

SITEFRAGDIR = ./html/

all : backup index.php tour.php story.php team.php contact.php PrivacyPolicy.php TermsOfService.php landing.php

backup : index.php tour.php story.php team.php contact.php PrivacyPolicy.php TermsOfService.php landing.php
	cp index.php tour.php story.php team.php contact.php PrivacyPolicy.php TermsOfService.php landing.php backup

index.php : indexhead.frag $(SITEFRAGDIR)menubar.frag indextext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag
	cat indexhead.frag $(SITEFRAGDIR)menubar.frag indextext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag > index.php
	cat indexhead.frag $(SITEFRAGDIR)menubar.frag indextext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag > missing.html
	cp missing.html ./error

tour.php : tourhead.frag $(SITEFRAGDIR)menubar.frag tourtext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag
	cat tourhead.frag $(SITEFRAGDIR)menubar.frag tourtext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag > tour.php

story.php : storyhead.frag $(SITEFRAGDIR)menubar.frag storytext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag
	cat storyhead.frag $(SITEFRAGDIR)menubar.frag storytext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag > story.php

team.php : teamhead.frag $(SITEFRAGDIR)menubar.frag teamtext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag
	cat teamhead.frag $(SITEFRAGDIR)menubar.frag teamtext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag > team.php

contact.php : contacthead.frag $(SITEFRAGDIR)menubar.frag contacttext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag
	cat contacthead.frag $(SITEFRAGDIR)menubar.frag contacttext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag > contact.php

PrivacyPolicy.php : PrivacyPolicyhead.frag $(SITEFRAGDIR)menubar.frag PrivacyPolicytext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag
	cat PrivacyPolicyhead.frag $(SITEFRAGDIR)menubar.frag PrivacyPolicytext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag

TermsOfService.php : TermsOfServicehead.frag $(SITEFRAGDIR)menubar.frag TermsOfServicetext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag
	cat TermsOfServicehead.frag $(SITEFRAGDIR)menubar.frag TermsOfServicetext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag

landing.php : landinghead.frag $(SITEFRAGDIR)menubar.frag landingtext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag
	cat landinghead.frag $(SITEFRAGDIR)menubar.frag landingtext.frag $(SITEFRAGDIR)mainsidebar.frag $(SITEFRAGDIR)end.frag landingfunctions.frag > landing.php
