/**
 * tooltip.js
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:38
 */
// Makes it easier to check if the selected element is preset
jQuery.fn.exists = function () {
  return this.length > 0;
};

function bindTooltips(unbindOnly) {
  var ttOwners = $('.has-tooltip');
  //Add Tooltips to .has-tooltip
  ttOwners.off('mouseenter');
  if (unbindOnly) {
    return;
  }
  ttOwners.on('mouseenter', function () {
    var owner = $(this);
    if (owner.children('.popover').exists()) {
      owner.children('.popover').stop(true, true);
    }

    var tt = new Tooltip(owner);

    tt.tooltip().addClass(tt.tblr())
        .css(tt.cssBefore())
        .animate(tt.cssGoal(), {
          duration: 500,
        });
    tt.owner().on('mouseleave', function () {
      tt.tooltip().animate(tt.cssAfter(), {
        duration: 750,
        queue: false,
        complete: function () {
          tt.done();
        }
      });
    });
  });
}

function Tooltip(jQueryEventOwner) {
  var owner = {
        _owner: null,
        height: function () {
          return owner._owner.innerHeight();
        },
        position: function () {
          return owner._owner.position();
        },
        width: function () {
          return owner._owner.innerWidth();
        }
      },
      tooltip = {
        _body: null,
        _template: '<div class="popover"><div class="popover-content">#CONTENT#</div><span class="arrow"></span></div>',
        appendToBody: function () {
          return (owner._owner.attr('tooltip-to-body') == '1');
        },
        content: function () {
          return owner._owner.attr('tooltip');
        },
        height: function () {
          return tooltip._body.outerHeight();
        },
        width: function () {
          return tooltip._body.outerWidth();
        },
        tblr: function () {
          return owner._owner.attr('tooltip-position');
        },
        userOffset: function () {
          return {
            left: parseInt(owner._owner.attr('tooltip-offset').split(',')[0]) || 0,
            top: parseInt(owner._owner.attr('tooltip-offset').split(',')[1]) || 0,
          }
        },
      },
      variants = {
        top: function () {
          animation.cssGoal.left = owner.position().left - (tooltip.width() / 2 - owner.width() / 2) + tooltip.userOffset().left;
          animation.cssGoal.top = owner.position().top - tooltip.height() + tooltip.userOffset().top;
          animation.cssBefore.top = (owner.height() * animation.tooltipScaleMultiplier) + animation.cssGoal.top;
          animation.cssBefore.left = animation.cssGoal.left;
          animation.cssAfter.top = animation.cssGoal.top - animation.endOffset;
          animation.cssAfter.left = animation.cssGoal.left;
        },
        bottom: function () {
          animation.cssGoal.left = owner.position().left - (tooltip.width() / 2 - owner.width() / 2) + tooltip.userOffset().left;
          animation.cssGoal.top = owner.position().top + tooltip.height() + tooltip.userOffset().top;
          animation.cssBefore.top = animation.cssGoal.top - (owner.height() * animation.tooltipScaleMultiplier);
          animation.cssBefore.left = animation.cssGoal.left;
          animation.cssAfter.top = animation.cssGoal.top - animation.endOffset;
          animation.cssAfter.left = animation.cssGoal.left;
        },
        left: function () {
          animation.cssGoal.left = owner.position().left - tooltip.width() + tooltip.userOffset().left;
          animation.cssGoal.top = owner.position().top - (tooltip.height() / 2 - owner.height() / 2) + tooltip.userOffset().top;
          animation.cssBefore.top = animation.cssGoal.top;
          animation.cssBefore.left = (owner.width() * animation.tooltipScaleMultiplier) + animation.cssGoal.left;
          animation.cssAfter.top = animation.cssGoal.top;
          animation.cssAfter.left = animation.cssGoal.left - animation.endOffset;
        },
        right: function () {
          animation.cssGoal.left = owner.position().left + owner.width() + tooltip.userOffset().left;
          animation.cssGoal.top = owner.position().top + (tooltip.height() / 2 - owner.height() / 2) + tooltip.userOffset().top;
          animation.cssBefore.top = animation.cssGoal.top;
          animation.cssBefore.left = animation.cssGoal.left - (owner.width() * animation.tooltipScaleMultiplier);
          animation.cssAfter.top = animation.cssGoal.top;
          animation.cssAfter.left = animation.cssGoal.left + animation.endOffset;
        },
        event: function () {
          animation.cssGoal.left = event.clientX;
          animation.cssGoal.top = event.clientY;
        }
      },
      animation = {
        cssBefore: {
          transform: 'scale(1)',
        },
        cssGoal: {
          opacity: 1,
          transform: 'scale(1)',
        },
        cssAfter: {
          opacity: 0,
          transform: ''
        },
        endOffset: 50,
        tooltipScaleMultiplier: 1.3,
      };

  owner._owner = jQueryEventOwner;
  tooltip._body = $(tooltip._template.replace('#CONTENT#', tooltip.content()));
  if (tooltip.appendToBody()) {
    $('body').append(tooltip._body);
  } else {
    owner._owner.append(tooltip._body);
  }
  variants[tooltip.tblr()]();

  this.cssBefore = function () {
    return animation.cssBefore;
  };
  this.cssAfter = function () {
    return animation.cssAfter;
  };
  this.cssGoal = function () {
    return animation.cssGoal;
  };
  this.tblr = function () {
    return tooltip.tblr()
  };
  this.owner = function () {
    return owner._owner;
  };
  this.tooltip = function () {
    return tooltip._body;
  };
  this.done = function () {
    owner._owner.off('mouseleave');
    tooltip._body.remove();
  }
}