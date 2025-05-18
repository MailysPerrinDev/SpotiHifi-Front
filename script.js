function afficher_form(id)
{
    var deja_present = 0;
    var ids= ['pseudo', 'mail', 'mdp', 'photo_modif'];
    
    if (document.getElementById(ids[id]).style.display != "none")
    {
        deja_present = 1;
    }
    
    for(i = 0; i< ids.length; i += 1)
    {
        document.getElementById(ids[i]).style.display = "none";
    }
    
    if(deja_present)
    {
        document.getElementById(ids[id]).style.display = "none";
    }
    else
    {
        document.getElementById(ids[id]).style.display = "flex";
    }
}

function recherche_tag()
{
    document.getElementById("recherche").value = button.name;
}