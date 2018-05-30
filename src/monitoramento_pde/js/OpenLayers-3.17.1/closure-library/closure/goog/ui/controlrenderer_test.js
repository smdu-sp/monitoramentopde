// Copyright 2008 The Closure Library Authors. All Rights Reserved.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS-IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

goog.provide('goog.ui.ControlRendererTest');
goog.setTestOnly('goog.ui.ControlRendererTest');

goog.require('goog.a11y.aria');
goog.require('goog.a11y.aria.Role');
goog.require('goog.a11y.aria.State');
goog.require('goog.dom');
goog.require('goog.dom.NodeType');
goog.require('goog.dom.TagName');
goog.require('goog.dom.classlist');
goog.require('goog.object');
goog.require('goog.style');
goog.require('goog.testing.ExpectedFailures');
goog.require('goog.testing.PropertyReplacer');
goog.require('goog.testing.jsunit');
goog.require('goog.ui.Component');
goog.require('goog.ui.Control');
goog.require('goog.ui.ControlRenderer');
goog.require('goog.userAgent');

var control, controlRenderer, testRenderer, propertyReplacer;
var sandbox;
var expectedFailures;

function setUpPage() {
  sandbox = goog.dom.getElement('sandbox');
  expectedFailures = new goog.testing.ExpectedFailures();
}



/**
 * A subclass of ControlRenderer that overrides {@code getAriaRole} and
 * {@code getStructuralCssClass} for testing purposes.
 * @constructor
 * @extends {goog.ui.ControlRenderer}
 */
function TestRenderer() {
  goog.ui.ControlRenderer.call(this);
}
goog.inherits(TestRenderer, goog.ui.ControlRenderer);
goog.addSingletonGetter(TestRenderer);

TestRenderer.CSS_CLASS = 'goog-button';

TestRenderer.IE6_CLASS_COMBINATIONS = [
  ['combined', 'goog-base-hover', 'goog-button'],
  ['combined', 'goog-base-disabled', 'goog-button'],
  ['combined', 'combined2', 'goog-base-hover', 'goog-base-rtl', 'goog-button']
];


/** @override */
TestRenderer.prototype.getAriaRole = function() {
  return goog.a11y.aria.Role.BUTTON;
};


/** @override */
TestRenderer.prototype.getCssClass = function() {
  return TestRenderer.CSS_CLASS;
};


/** @override */
TestRenderer.prototype.getStructuralCssClass = function() {
  return 'goog-base';
};


/** @override */
TestRenderer.prototype.getIe6ClassCombinations = function() {
  return TestRenderer.IE6_CLASS_COMBINATIONS;
};


/**
 * @return {boolean} Whether we're on Mac Safari 3.x.
 */
function isMacSafari3() {
  return goog.userAgent.WEBKIT && goog.userAgent.MAC &&
      !goog.userAgent.isVersionOrHigher('527');
}


/**
 * @return {boolean} Whether we're on IE6 or lower.
 */
function isIe6() {
  return goog.userAgent.IE && !goog.userAgent.isVersionOrHigher('7');
}

function setUp() {
  control = new goog.ui.Control('Hello');
  controlRenderer = goog.ui.ControlRenderer.getInstance();
  testRenderer = TestRenderer.getInstance();
  propertyReplacer = new goog.testing.PropertyReplacer();
}

function tearDown() {
  propertyReplacer.reset();
  control.dispose();
  expectedFailures.handleTearDown();
  control = null;
  controlRenderer = null;
  testRenderer = null;
  goog.dom.removeChildren(sandbox);
}

function testConstructor() {
  assertNotNull(
      'ControlRenderer singleton instance must not be null', controlRenderer);
  assertNotNull(
      'TestRenderer singleton instance must not be null', testRenderer);
}

function testGetCustomRenderer() {
  var cssClass = 'special-css-class';
  var renderer = goog.ui.ControlRenderer.getCustomRenderer(
      goog.ui.ControlRenderer, cssClass);
  assertEquals(
      'Renderer should have returned the custom CSS class.', cssClass,
      renderer.getCssClass());
}

function testGetAriaRole() {
  assertUndefined(
      'ControlRenderer\'s ARIA role must be undefined',
      controlRenderer.getAriaRole());
  assertEquals(
      'TestRenderer\'s ARIA role must have expected value',
      goog.a11y.aria.Role.BUTTON, testRenderer.getAriaRole());
}

function testCreateDom() {
  assertHTMLEquals(
      'ControlRenderer must create correct DOM',
      '<div class="goog-control">Hello</div>',
      goog.dom.getOuterHtml(controlRenderer.createDom(control)));
  assertHTMLEquals(
      'TestRenderer must create correct DOM',
      '<div class="goog-button goog-base">Hello</div>',
      goog.dom.getOuterHtml(testRenderer.createDom(control)));
}

function testGetContentElement() {
  assertEquals(
      'getContentElement() must return its argument', sandbox,
      controlRenderer.getContentElement(sandbox));
}

function testEnableExtraClassName() {
  // enableExtraClassName() must be a no-op if control has no DOM.
  controlRenderer.enableExtraClassName(control, 'foo', true);

  control.createDom();
  var element = control.getElement();

  controlRenderer.enableExtraClassName(control, 'foo', true);
  assertSameElements(
      'Extra class name must have been added', ['goog-control', 'foo'],
      goog.dom.classlist.get(element));

  controlRenderer.enableExtraClassName(control, 'foo', true);
  assertSameElements(
      'Enabling existing extra class name must be a no-op',
      ['goog-control', 'foo'], goog.dom.classlist.get(element));

  controlRenderer.enableExtraClassName(control, 'bar', false);
  assertSameElements(
      'Disabling nonexistent class name must be a no-op',
      ['goog-control', 'foo'], goog.dom.classlist.get(element));

  controlRenderer.enableExtraClassName(control, 'foo', false);
  assertSameElements(
      'Extra class name must have been removed', ['goog-control'],
      goog.dom.classlist.get(element));
}

function testCanDecorate() {
  assertTrue('canDecorate() must return true', controlRenderer.canDecorate());
}

function testDecorate() {
  sandbox.innerHTML = '<div id="foo">Hello, world!</div>';
  var foo = goog.dom.getElement('foo');
  var element = controlRenderer.decorate(control, foo);

  assertEquals('decorate() must return its argument', foo, element);
  assertEquals('Decorated control\'s ID must be set', 'foo', control.getId());
  assertTrue(
      'Decorated control\'s content must be a text node',
      control.getContent().nodeType == goog.dom.NodeType.TEXT);
  assertEquals(
      'Decorated control\'s content must have expected value', 'Hello, world!',
      control.getContent().nodeValue);
  assertEquals(
      'Decorated control\'s state must be as expected', 0x00,
      control.getState());
  assertSameElements(
      'Decorated element\'s classes must be as expected', ['goog-control'],
      goog.dom.classlist.get(element));
}

function testDecorateComplexDom() {
  sandbox.innerHTML = '<div id="foo"><i>Hello</i>,<b>world</b>!</div>';
  var foo = goog.dom.getElement('foo');
  var element = controlRenderer.decorate(control, foo);

  assertEquals('decorate() must return its argument', foo, element);
  assertEquals('Decorated control\'s ID must be set', 'foo', control.getId());
  assertTrue(
      'Decorated control\'s content must be an array',
      goog.isArray(control.getContent()));
  assertEquals(
      'Decorated control\'s content must have expected length', 4,
      control.getContent().length);
  assertEquals(
      'Decorated control\'s state must be as expected', 0x00,
      control.getState());
  assertSameElements(
      'Decorated element\'s classes must be as expected', ['goog-control'],
      goog.dom.classlist.get(element));
}

function testDecorateWithClasses() {
  sandbox.innerHTML =
      '<div id="foo" class="app goog-base-disabled goog-base-hover"></div>';
  var foo = goog.dom.getElement('foo');

  control.addClassName('extra');
  var element = testRenderer.decorate(control, foo);

  assertEquals('decorate() must return its argument', foo, element);
  assertEquals('Decorated control\'s ID must be set', 'foo', control.getId());
  assertNull('Decorated control\'s content must be null', control.getContent());
  assertEquals(
      'Decorated control\'s state must be as expected',
      goog.ui.Component.State.DISABLED | goog.ui.Component.State.HOVER,
      control.getState());
  assertSameElements(
      'Decorated element\'s classes must be as expected',
      [
        'app', 'extra', 'goog-base', 'goog-base-disabled', 'goog-base-hover',
        'goog-button'
      ],
      goog.dom.classlist.get(element));
}

function testDecorateOptimization() {
  // Temporarily replace goog.dom.classlist.set().
  propertyReplacer.set(goog.dom.classlist, 'set', function() {
    fail('goog.dom.classlist.set() must not be called');
  });

  // Since foo has all required classes, goog.dom.classlist.set() must not be
  // called at all.
  sandbox.innerHTML = '<div id="foo" class="goog-control">Foo</div>';
  controlRenderer.decorate(control, goog.dom.getElement('foo'));

  // Since bar has all required classes, goog.dom.classlist.set() must not be
  // called at all.
  sandbox.innerHTML = '<div id="bar" class="goog-base goog-button">Bar' +
      '</div>';
  testRenderer.decorate(control, goog.dom.getElement('bar'));

  // Since baz has all required classes, goog.dom.classlist.set() must not be
  // called at all.
  sandbox.innerHTML = '<div id="baz" class="goog-base goog-button ' +
      'goog-button-disabled">Baz</div>';
  testRenderer.decorate(control, goog.dom.getElement('baz'));
}

function testInitializeDom() {
  var renderer = new goog.ui.ControlRenderer();

  // Replace setRightToLeft().
  renderer.setRightToLeft = function() {
    fail('setRightToLeft() must not be called');
  };

  // When a control with default render direction enters the document,
  // setRightToLeft() must not be called.
  control.setRenderer(renderer);
  control.render(sandbox);

  // When a control in the default state (enabled, visible, focusable)
  // enters the document, it must get a tab index.
  // Expected to fail on Mac Safari 3, because it doesn't support tab index.
  expectedFailures.expectFailureFor(isMacSafari3());
  try {
    assertTrue(
        'Enabled, visible, focusable control must have tab index',
        goog.dom.isFocusableTabIndex(control.getElement()));
  } catch (e) {
    expectedFailures.handleException(e);
  }
}

function testInitializeDomDecorated() {
  var renderer = new goog.ui.ControlRenderer();

  // Replace setRightToLeft().
  renderer.setRightToLeft = function() {
    fail('setRightToLeft() must not be called');
  };

  sandbox.innerHTML = '<div id="foo" class="goog-control">Foo</div>';

  // When a control with default render direction enters the document,
  // setRightToLeft() must not be called.
  control.setRenderer(renderer);
  control.decorate(goog.dom.getElement('foo'));

  // When a control in the default state (enabled, visible, focusable)
  // enters the document, it must get a tab index.
  // Expected to fail on Mac Safari 3, because it doesn't support tab index.
  expectedFailures.expectFailureFor(isMacSafari3());
  try {
    assertTrue(
        'Enabled, visible, focusable control must have tab index',
        goog.dom.isFocusableTabIndex(control.getElement()));
  } catch (e) {
    expectedFailures.handleException(e);
  }
}

function testInitializeDomDisabledBiDi() {
  var renderer = new goog.ui.ControlRenderer();

  // Replace setFocusable().
  renderer.setFocusable = function() {
    fail('setFocusable() must not be called');
  };

  // When a disabled control enters the document, setFocusable() must not
  // be called.
  control.setEnabled(false);
  control.setRightToLeft(true);
  control.setRenderer(renderer);
  control.render(sandbox);

  // When a right-to-left control enters the document, special stying must
  // be applied.
  assertSameElements(
      'BiDi control must have right-to-left class',
      ['goog-control', 'goog-control-disabled', 'goog-control-rtl'],
      goog.dom.classlist.get(control.getElement()));
}

function testInitializeDomDisabledBiDiDecorated() {
  var renderer = new goog.ui.ControlRenderer();

  // Replace setFocusable().
  renderer.setFocusable = function() {
    fail('setFocusable() must not be called');
  };

  sandbox.innerHTML = '<div dir="rtl">\n' +
      '  <div id="foo" class="goog-control-disabled">Foo</div>\n' +
      '</div>\n';

  // When a disabled control enters the document, setFocusable() must not
  // be called.
  control.setRenderer(renderer);
  control.decorate(goog.dom.getElement('foo'));

  // When a right-to-left control enters the document, special stying must
  // be applied.
  assertSameElements(
      'BiDi control must have right-to-left class',
      ['goog-control', 'goog-control-disabled', 'goog-control-rtl'],
      goog.dom.classlist.get(control.getElement()));
}

function testSetAriaRole() {
  sandbox.innerHTML = '<div id="foo">Foo</div><div id="bar">Bar</div>';

  var foo = goog.dom.getElement('foo');
  assertNotNull(foo);
  controlRenderer.setAriaRole(foo);
  assertEvaluatesToFalse(
      'The role should be empty.', goog.a11y.aria.getRole(foo));
  var bar = goog.dom.getElement('bar');
  assertNotNull(bar);
  testRenderer.setAriaRole(bar);
  assertEquals(
      'Element must have expected ARIA role', goog.a11y.aria.Role.BUTTON,
      goog.a11y.aria.getRole(bar));
}

function testSetAriaStatesHidden() {
  sandbox.innerHTML = '<div id="foo">Foo</div><div id="bar">Bar</div>';
  var foo = goog.dom.getElement('foo');

  control.setVisible(true);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-hidden.', '',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.HIDDEN));

  control.setVisible(false);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-hidden.', 'true',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.HIDDEN));
}

function testSetAriaStatesDisabled() {
  sandbox.innerHTML = '<div id="foo">Foo</div><div id="bar">Bar</div>';
  var foo = goog.dom.getElement('foo');

  control.setEnabled(true);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-disabled.', '',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.DISABLED));

  control.setEnabled(false);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-disabled.', 'true',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.DISABLED));
}

function testSetAriaStatesSelected() {
  sandbox.innerHTML = '<div id="foo">Foo</div><div id="bar">Bar</div>';
  var foo = goog.dom.getElement('foo');
  control.setSupportedState(goog.ui.Component.State.SELECTED, true);

  control.setSelected(true);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-selected.', 'true',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.SELECTED));

  control.setSelected(false);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-selected.', 'false',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.SELECTED));
}

function testSetAriaStatesChecked() {
  sandbox.innerHTML = '<div id="foo">Foo</div><div id="bar">Bar</div>';
  var foo = goog.dom.getElement('foo');
  control.setSupportedState(goog.ui.Component.State.CHECKED, true);

  control.setChecked(true);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-checked.', 'true',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.CHECKED));

  control.setChecked(false);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-checked.', 'false',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.CHECKED));
}

function testSetAriaStatesExpanded() {
  sandbox.innerHTML = '<div id="foo">Foo</div><div id="bar">Bar</div>';
  var foo = goog.dom.getElement('foo');
  control.setSupportedState(goog.ui.Component.State.OPENED, true);

  control.setOpen(true);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-expanded.', 'true',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.EXPANDED));

  control.setOpen(false);
  controlRenderer.setAriaStates(control, foo);

  assertEquals(
      'ControlRenderer did not set aria-expanded.', 'false',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.EXPANDED));
}

function testSetAllowTextSelection() {
  sandbox.innerHTML = '<div id="foo"><span>Foo</span></div>';
  var foo = goog.dom.getElement('foo');

  controlRenderer.setAllowTextSelection(foo, false);
  assertTrue(
      'Parent element must be unselectable on all browsers',
      goog.style.isUnselectable(foo));
  if (goog.userAgent.IE || goog.userAgent.OPERA) {
    assertTrue(
        'On IE and Opera, child element must also be unselectable',
        goog.style.isUnselectable(foo.firstChild));
  } else {
    assertFalse(
        'On browsers other than IE and Opera, the child element ' +
            'must not be unselectable',
        goog.style.isUnselectable(foo.firstChild));
  }

  controlRenderer.setAllowTextSelection(foo, true);
  assertFalse(
      'Parent element must be selectable', goog.style.isUnselectable(foo));
  assertFalse(
      'Child element must be unselectable',
      goog.style.isUnselectable(foo.firstChild));
}

function testSetRightToLeft() {
  sandbox.innerHTML = '<div id="foo">Foo</div><div id="bar">Bar</div>';

  var foo = goog.dom.getElement('foo');
  controlRenderer.setRightToLeft(foo, true);
  assertSameElements(
      'Element must have right-to-left class applied', ['goog-control-rtl'],
      goog.dom.classlist.get(foo));
  controlRenderer.setRightToLeft(foo, false);
  assertSameElements(
      'Element must not have right-to-left class applied', [],
      goog.dom.classlist.get(foo));

  var bar = goog.dom.getElement('bar');
  testRenderer.setRightToLeft(bar, true);
  assertSameElements(
      'Element must have right-to-left class applied', ['goog-base-rtl'],
      goog.dom.classlist.get(bar));
  testRenderer.setRightToLeft(bar, false);
  assertSameElements(
      'Element must not have right-to-left class applied', [],
      goog.dom.classlist.get(bar));
}

function testIsFocusable() {
  control.render(sandbox);
  // Expected to fail on Mac Safari 3, because it doesn't support tab index.
  expectedFailures.expectFailureFor(isMacSafari3());
  try {
    assertTrue(
        'Control\'s key event target must be focusable',
        controlRenderer.isFocusable(control));
  } catch (e) {
    expectedFailures.handleException(e);
  }
}

function testIsFocusableForNonFocusableControl() {
  control.setSupportedState(goog.ui.Component.State.FOCUSED, false);
  control.render(sandbox);
  assertFalse(
      'Non-focusable control\'s key event target must not be ' +
          'focusable',
      controlRenderer.isFocusable(control));
}

function testIsFocusableForControlWithoutKeyEventTarget() {
  // Unrendered control has no key event target.
  assertNull(
      'Unrendered control must not have key event target',
      control.getKeyEventTarget());
  assertFalse(
      'isFocusable() must return null if no key event target',
      controlRenderer.isFocusable(control));
}

function testSetFocusable() {
  control.render(sandbox);
  controlRenderer.setFocusable(control, false);
  assertFalse(
      'Control\'s key event target must not have tab index',
      goog.dom.isFocusableTabIndex(control.getKeyEventTarget()));
  controlRenderer.setFocusable(control, true);
  // Expected to fail on Mac Safari 3, because it doesn't support tab index.
  expectedFailures.expectFailureFor(isMacSafari3());
  try {
    assertTrue(
        'Control\'s key event target must have focusable tab index',
        goog.dom.isFocusableTabIndex(control.getKeyEventTarget()));
  } catch (e) {
    expectedFailures.handleException(e);
  }
}

function testSetFocusableForNonFocusableControl() {
  control.setSupportedState(goog.ui.Component.State.FOCUSED, false);
  control.render(sandbox);
  assertFalse(
      'Non-focusable control\'s key event target must not be ' +
          'focusable',
      goog.dom.isFocusableTabIndex(control.getKeyEventTarget()));
  controlRenderer.setFocusable(control, true);
  assertFalse(
      'Non-focusable control\'s key event target must not be ' +
          'focusable, even after calling setFocusable(true)',
      goog.dom.isFocusableTabIndex(control.getKeyEventTarget()));
}

function testSetVisible() {
  sandbox.innerHTML = '<div id="foo">Foo</div>';
  var foo = goog.dom.getElement('foo');
  assertTrue('Element must be visible', foo.style.display != 'none');
  controlRenderer.setVisible(foo, true);
  assertEquals(
      'ControlRenderer did not set aria-hidden.', 'false',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.HIDDEN));
  assertTrue('Element must still be visible', foo.style.display != 'none');
  controlRenderer.setVisible(foo, false);
  assertEquals(
      'ControlRenderer did not set aria-hidden.', 'true',
      goog.a11y.aria.getState(foo, goog.a11y.aria.State.HIDDEN));
  assertTrue('Element must be hidden', foo.style.display == 'none');
}

function testSetState() {
  control.setRenderer(testRenderer);
  control.createDom();
  var element = control.getElement();
  assertNotNull(element);
  assertSameElements(
      'Control must have expected class names', ['goog-button', 'goog-base'],
      goog.dom.classlist.get(element));
  assertEquals(
      'Control must not have disabled ARIA state', '',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));

  testRenderer.setState(control, goog.ui.Component.State.DISABLED, true);
  assertSameElements(
      'Control must have disabled class name',
      ['goog-button', 'goog-base', 'goog-base-disabled'],
      goog.dom.classlist.get(element));
  assertEquals(
      'Control must have disabled ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));

  testRenderer.setState(control, goog.ui.Component.State.DISABLED, false);
  assertSameElements(
      'Control must no longer have disabled class name',
      ['goog-button', 'goog-base'], goog.dom.classlist.get(element));
  assertEquals(
      'Control must not have disabled ARIA state', 'false',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));

  testRenderer.setState(control, 0xFFFFFF, true);
  assertSameElements(
      'Class names must be unchanged for invalid state',
      ['goog-button', 'goog-base'], goog.dom.classlist.get(element));
}

function testUpdateAriaStateDisabled() {
  control.createDom();
  var element = control.getElement();
  assertNotNull(element);
  controlRenderer.updateAriaState(
      element, goog.ui.Component.State.DISABLED, true);
  assertEquals(
      'Control must have disabled ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));

  controlRenderer.updateAriaState(
      element, goog.ui.Component.State.DISABLED, false);
  assertEquals(
      'Control must no longer have disabled ARIA state', 'false',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));
}

function testSetAriaStatesRender_ariaStateDisabled() {
  control.setEnabled(false);
  var renderer = new goog.ui.ControlRenderer();
  control.setRenderer(renderer);
  control.render(sandbox);
  var element = control.getElement();
  assertNotNull(element);
  assertFalse('Control must be disabled', control.isEnabled());
  assertEquals(
      'Control must have disabled ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));
}

function testSetAriaStatesDecorate_ariaStateDisabled() {
  sandbox.innerHTML = '<div id="foo" class="app goog-base-disabled"></div>';
  var element = goog.dom.getElement('foo');

  control.setRenderer(testRenderer);
  control.decorate(element);
  assertNotNull(element);
  assertFalse('Control must be disabled', control.isEnabled());
  assertEquals(
      'Control must have disabled ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));
}

function testUpdateAriaStateSelected() {
  control.createDom();
  var element = control.getElement();
  assertNotNull(element);
  controlRenderer.updateAriaState(
      element, goog.ui.Component.State.SELECTED, true);
  assertEquals(
      'Control must have selected ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.SELECTED));

  controlRenderer.updateAriaState(
      element, goog.ui.Component.State.SELECTED, false);
  assertEquals(
      'Control must no longer have selected ARIA state', 'false',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.SELECTED));
}

function testSetAriaStatesRender_ariaStateSelected() {
  control.setSupportedState(goog.ui.Component.State.SELECTED, true);
  control.setSelected(true);

  var renderer = new goog.ui.ControlRenderer();
  control.setRenderer(renderer);
  control.render(sandbox);
  var element = control.getElement();
  assertNotNull(element);
  assertTrue('Control must be selected', control.isSelected());
  assertEquals(
      'Control must have selected ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.SELECTED));
}

function testSetAriaStatesRender_ariaStateNotSelected() {
  control.setSupportedState(goog.ui.Component.State.SELECTED, true);

  var renderer = new goog.ui.ControlRenderer();
  control.setRenderer(renderer);
  control.render(sandbox);
  var element = control.getElement();
  assertNotNull(element);
  assertFalse('Control must not be selected', control.isSelected());
  assertEquals(
      'Control must have not-selected ARIA state', 'false',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.SELECTED));
}

function testSetAriaStatesDecorate_ariaStateSelected() {
  control.setSupportedState(goog.ui.Component.State.SELECTED, true);

  sandbox.innerHTML = '<div id="foo" class="app goog-control-selected"></div>';
  var element = goog.dom.getElement('foo');

  control.setRenderer(controlRenderer);
  control.decorate(element);
  assertNotNull(element);
  assertTrue('Control must be selected', control.isSelected());
  assertEquals(
      'Control must have selected ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.SELECTED));
}

function testUpdateAriaStateChecked() {
  control.createDom();
  var element = control.getElement();
  assertNotNull(element);
  controlRenderer.updateAriaState(
      element, goog.ui.Component.State.CHECKED, true);
  assertEquals(
      'Control must have checked ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.CHECKED));

  controlRenderer.updateAriaState(
      element, goog.ui.Component.State.CHECKED, false);
  assertEquals(
      'Control must no longer have checked ARIA state', 'false',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.CHECKED));
}

function testSetAriaStatesRender_ariaStateChecked() {
  control.setSupportedState(goog.ui.Component.State.CHECKED, true);
  control.setChecked(true);

  var renderer = new goog.ui.ControlRenderer();
  control.setRenderer(renderer);
  control.render(sandbox);
  var element = control.getElement();
  assertNotNull(element);
  assertTrue('Control must be checked', control.isChecked());
  assertEquals(
      'Control must have checked ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.CHECKED));
}

function testSetAriaStatesDecorate_ariaStateChecked() {
  sandbox.innerHTML = '<div id="foo" class="app goog-control-checked"></div>';
  var element = goog.dom.getElement('foo');

  control.setSupportedState(goog.ui.Component.State.CHECKED, true);
  control.decorate(element);
  assertNotNull(element);
  assertTrue('Control must be checked', control.isChecked());
  assertEquals(
      'Control must have checked ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.CHECKED));
}

function testUpdateAriaStateOpened() {
  control.createDom();
  var element = control.getElement();
  assertNotNull(element);
  controlRenderer.updateAriaState(
      element, goog.ui.Component.State.OPENED, true);
  assertEquals(
      'Control must have expanded ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.EXPANDED));

  controlRenderer.updateAriaState(
      element, goog.ui.Component.State.OPENED, false);
  assertEquals(
      'Control must no longer have expanded ARIA state', 'false',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.EXPANDED));
}

function testSetAriaStatesRender_ariaStateOpened() {
  control.setSupportedState(goog.ui.Component.State.OPENED, true);
  control.setOpen(true);

  var renderer = new goog.ui.ControlRenderer();
  control.setRenderer(renderer);
  control.render(sandbox);
  var element = control.getElement();
  assertNotNull(element);
  assertTrue('Control must be opened', control.isOpen());
  assertEquals(
      'Control must have expanded ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.EXPANDED));
}

function testSetAriaStatesDecorate_ariaStateOpened() {
  sandbox.innerHTML = '<div id="foo" class="app goog-base-open"></div>';
  var element = goog.dom.getElement('foo');

  control.setSupportedState(goog.ui.Component.State.OPENED, true);
  control.setRenderer(testRenderer);
  control.decorate(element);
  assertNotNull(element);
  assertTrue('Control must be opened', control.isOpen());
  assertEquals(
      'Control must have expanded ARIA state', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.EXPANDED));
}

function testSetAriaStateRoleNotInMap() {
  sandbox.innerHTML = '<div id="foo" role="option">Hello, world!</div>';
  control.setRenderer(controlRenderer);
  control.setSupportedState(goog.ui.Component.State.CHECKED, true);
  var element = goog.dom.getElement('foo');
  control.decorate(element);
  assertEquals(
      'Element should have ARIA role option.', goog.a11y.aria.Role.OPTION,
      goog.a11y.aria.getRole(element));
  control.setStateInternal(goog.ui.Component.State.DISABLED, true);
  controlRenderer.setAriaStates(control, element);
  assertEquals(
      'Element should have aria-disabled true', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));
  control.setStateInternal(goog.ui.Component.State.CHECKED, true);
  controlRenderer.setAriaStates(control, element);
  assertEquals(
      'Element should have aria-checked true', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.CHECKED));
}

function testSetAriaStateRoleInMapMatches() {
  sandbox.innerHTML = '<div id="foo" role="checkbox">Hello, world!</div>';
  control.setRenderer(controlRenderer);
  control.setSupportedState(goog.ui.Component.State.CHECKED, true);
  var element = goog.dom.getElement('foo');
  control.decorate(element);
  assertEquals(
      'Element should have ARIA role checkbox.', goog.a11y.aria.Role.CHECKBOX,
      goog.a11y.aria.getRole(element));
  control.setStateInternal(goog.ui.Component.State.DISABLED, true);
  controlRenderer.setAriaStates(control, element);
  assertEquals(
      'Element should have aria-disabled true', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));
  control.setStateInternal(goog.ui.Component.State.CHECKED, true);
  controlRenderer.setAriaStates(control, element);
  assertEquals(
      'Element should have aria-checked true', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.CHECKED));
}

function testSetAriaStateRoleInMapNotMatches() {
  sandbox.innerHTML = '<div id="foo" role="button">Hello, world!</div>';
  control.setRenderer(controlRenderer);
  control.setSupportedState(goog.ui.Component.State.CHECKED, true);
  var element = goog.dom.getElement('foo');
  control.decorate(element);
  assertEquals(
      'Element should have ARIA role button.', goog.a11y.aria.Role.BUTTON,
      goog.a11y.aria.getRole(element));
  control.setStateInternal(goog.ui.Component.State.DISABLED, true);
  controlRenderer.setAriaStates(control, element);
  assertEquals(
      'Element should have aria-disabled true', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.DISABLED));
  control.setStateInternal(goog.ui.Component.State.CHECKED, true);
  controlRenderer.setAriaStates(control, element);
  assertEquals(
      'Element should have aria-pressed true', 'true',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.PRESSED));
  assertEquals(
      'Element should not have aria-checked', '',
      goog.a11y.aria.getState(element, goog.a11y.aria.State.CHECKED));
}

function testToggleAriaStateMap() {
  var map = goog.ob                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    