# Info

almost any link that starts with / slash mush have own page inside views.master.pages if not declared otherwise inside $selector->getViewName();
but $selector->getViewName() also sets $pageName that is used to determine which page to display to the user, you can also set page title inside $selector->getViewName() and then display it via {{$selector->title}}