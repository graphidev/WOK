(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['template.hb'] = template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, stack2;
  buffer += "\n        <div class=\"session\">\n            ";
  stack2 = helpers['if'].call(depth0, ((stack1 = depth0.session),stack1 == null || stack1 === false ? stack1 : stack1.firstname), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack2 || stack2 === 0) { buffer += stack2; }
  buffer += "\n            ";
  stack2 = helpers['if'].call(depth0, ((stack1 = depth0.session),stack1 == null || stack1 === false ? stack1 : stack1.error), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack2 || stack2 === 0) { buffer += stack2; }
  buffer += "\n        \n        </div>\n    ";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, stack2;
  buffer += " ";
  stack2 = helpers['if'].call(depth0, ((stack1 = depth0.session),stack1 == null || stack1 === false ? stack1 : stack1.lastname), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack2 || stack2 === 0) { buffer += stack2; }
  return buffer;
  }
function program3(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            Connecté en tant que : "
    + escapeExpression(((stack1 = ((stack1 = depth0.session),stack1 == null || stack1 === false ? stack1 : stack1.firstname)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + " "
    + escapeExpression(((stack1 = ((stack1 = depth0.session),stack1 == null || stack1 === false ? stack1 : stack1.lastname)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\n            ";
  return buffer;
  }

function program5(depth0,data) {
  
  
  return "\n                Erreur de connexion\n            ";
  }

function program7(depth0,data) {
  
  var buffer = "", stack1;
  buffer += " <div class=\"entry\">";
  if (stack1 = helpers.firstName) { stack1 = stack1.call(depth0, {hash:{},data:data}); }
  else { stack1 = depth0.firstName; stack1 = typeof stack1 === functionType ? stack1.apply(depth0) : stack1; }
  buffer += escapeExpression(stack1)
    + " ";
  if (stack1 = helpers.lastName) { stack1 = stack1.call(depth0, {hash:{},data:data}); }
  else { stack1 = depth0.lastName; stack1 = typeof stack1 === functionType ? stack1.apply(depth0) : stack1; }
  buffer += escapeExpression(stack1)
    + "</div> ";
  return buffer;
  }

  buffer += "\n\n    ";
  stack1 = helpers['if'].call(depth0, depth0.session, {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n\n";
  stack1 = helpers.each.call(depth0, depth0.people, {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  });
})();