function showmap(filename) {
  w = window.open("","","width=400,height=300,resizable=yes,scrollbars=yes");
  d = w.document;
  d.writeln("<html><bo"+"dy>");
  d.writeln("<img src='maps/" + filename + "' style=\"position: absolute; top: 0px; left: 0px;\" onMouseDown='window.close();'>");
  d.writeln("</bo"+"dy></html>");
}