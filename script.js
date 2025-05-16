function afficher_form(id)
{
    var ids= ['pseudo', 'mail', 'mdp'];
    
    for(i = 0; i< ids.length; i += 1)
    {
        document.getElementById(ids[i]).style.display = "none";
    }


    document.getElementById(ids[id]).style.display = "initial";
}
