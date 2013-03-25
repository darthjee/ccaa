function hide(id)
{
  document.getElementById(id).style.display='none';
}

function show(id)
{
  document.getElementById(id).style.display='';
}

function getDisplay(id)
{
  return document.getElementById(id).style.display;
}

function showHide(id)
{
  if (getDisplay(id) == 'none')
  {
    show(id);
    hide(id+'_bt');
  }
  else
  {
    hide(id);
    show(id+'_bt');
  }
}
