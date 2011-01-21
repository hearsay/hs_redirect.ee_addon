<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=========================================================================
Copyright (c) 2011 Kevin Smith <kevin@gohearsay.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
=========================================================================
 File: pi.hs_redirect.php V1.0.1
-------------------------------------------------------------------------
 Purpose: Redirects current EE template to a given location.
=========================================================================
CHANGE LOG :

January 20, 2011
	- Version 1.0.1
	- Fixed bug that caused the plugin to do nothing when logged_in param
	  wasn't included. Weird bug, frankly. Not sure how I missed that.

October 26, 2010
	- Version 1.0.0
	- First release
=========================================================================
*/

$plugin_info = array(
						'pi_name'			=> 'HS Redirect',
						'pi_version'		=> '1.0.1',
						'pi_author'			=> 'Kevin Smith',
						'pi_author_url'		=> 'http://www.gohearsay.com/',
						'pi_description'	=> 'Redirects current EE template to a given location.',
						'pi_usage'			=> Hs_redirect::usage()
					);

class Hs_redirect
{

    var $return_data = '';

	function hs_redirect()
	{
		$this->EE =& get_instance();
		
		// Fetch our parameters from the plugin tag
		$location = str_replace("&#47;", "/", $this->EE->TMPL->fetch_param('location'));
		$method = $this->EE->TMPL->fetch_param('method');
		
		// Check for the logged_in param.
		$logged_in = $this->EE->TMPL->fetch_param('logged_in');
		
		// Check for the group param.
		$member_group = $this->EE->TMPL->fetch_param('group_id');
		
		// Check to see if location should be set to http_referer
		$referrer = strtolower($this->EE->TMPL->fetch_param('referrer'));
		
		if ($logged_in === "yes")
		{
			$logged_in = ($this->EE->session->userdata('member_id') == 0) ? FALSE : TRUE; 
		}
		elseif ($logged_in === "no")
		{
			$logged_in = ($this->EE->session->userdata('member_id') != 0) ? FALSE : TRUE; 
		}
		else
		{
			$logged_in = TRUE;
		}
		
		// Is this a full URL?
		if (strpos($location, 'http') !== 0 AND !(strpos($location, 'http') > 0)) 
   		{
     		// If not, let's make it one.
 			$location = $this->EE->functions->create_url($location);
		}
		
		// Use HTTP_REFERER if referrer param equals 'true'.
		$location = (($referrer === 'true' && $this->EE->input->server('HTTP_REFERER')) ? $this->EE->input->server('HTTP_REFERER') : $location);
		
		if ($member_group)
		{
			// Is the visitor part of the member group entered into the group_id param?
			$member_group = ($member_group === $this->EE->session->userdata('group_id')) ? TRUE : FALSE;
		}
		else
		{
			$member_group = TRUE;
		}

		if ($member_group === TRUE AND $logged_in === TRUE)
		{		
			// Perform a check to see if method parameter supplied
			if ($method === FALSE)
			{
			   		// If we do not find method parameter then perform PHP redirect
			    $this->EE->functions->redirect($location);
			 }
				else
			 {
			  	// If we find method parameter then create redirection javascript and output it
			  	$output = '<script type="text/javascript">location.href="'.$location.'"</script>';
			  	$this->return_data = $output;
			}
		}
	}
// END


// ----------------------------------------
//  Plugin Usage
// ----------------------------------------
// This function describes how the plugin is used.
// Make sure and use output buffering

function usage()
{
ob_start(); 
?>

HS Redirect is a simple plugin that will redirect to an internal or external location. It can be used, for example, to redirect a visitor to a login page if they need to login before viewing the page.

=====================================================
Examples
=====================================================

External links:
{exp:hs_redirect location="http://www.google.com"}

Internal links (template paths):
{exp:hs_redirect location="blog/entry/12345"}

=====================================================
Parameters
=====================================================

method="script"
- PHP redirection is the default, but you can also choose to use javascript redirection by using the method="script" parameter. (This is useful if you need the current template to register as the referrer of the page to which this plugin is redirecting.)

logged_in=""
- Options are 'yes' or 'no'.
- If either values are entered in the parameter, the plugin will only be processed for visitors whose logged-in status matches the value.

group_id=""
- Enter the member group's numerical ID.
- If included, then the plugin will only be processed for visitors who are part of this group.

referrer="true"
- If included, then the plugin will use the web address of the page which referred to this template as the redirect location, effectively redirecting back to the same page that linked to this one.
- The fallback, if the $_SERVER["HTTP_REFERER"] variable cannot be determined, is to use the location parameter.
- When combined with logged_in="yes", this is helpful to keep login pages from accidentally being displayed to logged-in visitors, for example.

<?php

$buffer = ob_get_contents();

ob_end_clean(); 

return $buffer;
}
// END
}
// END CLASS

/* End of file pi.hs_redirect.php */
/* Location: ./system/expressionengine/third_party/hs_redirect/pi.hs_redirect.php */