<?php

App::uses('MailchimpAppModel', 'Mailchimp.Model');

class MailchimpCampaign extends MailchimpAppModel {

	/**
	 * Get the list of campaigns and their details matching the specified filters
	 *
	 * @section Campaign  Related
	 * @example mcapi_campaigns.php
	 * @example xml-rpc_campaigns.php
	 *
	 * @param array $filters a hash of filters to apply to this query - all are optional:
	 * @param integer $start optional - control paging of campaigns, start results at this campaign #, defaults to 1st page of data  (page 0)
	 * @param integer $limit optional - control paging of campaigns, number of campaigns to return with each call, defaults to 25 (max=1000)
	 * @return array an array containing a count of all matching campaigns and the specific ones for the current page (see Returned Fields for description)
	 * @returnf int total the total number of campaigns matching the filters passed in
	 * @returnf array data the data for each campaign being returned
	 */
	public function campaigns($filters = array(), $start = 0, $limit = 25) {
		return $this->Mailchimp->campaigns($filters, $start, $limit);
	}

	/**
	 * Search all campaigns for the specified query terms
	 *
	 * @section Helper
	 *
	 * @param string $query terms to search on
	 * @param integer offset optional the paging offset to use if more than 100 records match
	 * @param string snip_start optional by default clear text is returned. To have the match highlighted with something (like a strong HTML tag), <strong>both</strong> this and "snip_end" must be passed. You're on your own to not break the tags - 25 character max.
	 * @param string snip_end optional see "snip_start" above.
	 * @return array An array containing the total matches and current results
	 * int total total campaigns matching
	 * array results matching campaigns and snippets
	 * string snippet the matching snippet for the campaign
	 * array campaign the matching campaign's details - will return same data as single campaign from campaigns()
	 */
	public function search($query, $offset = 0, $snipStart = null, $snipEnd = null) {
		return $this->Mailchimp->searchCampaigns($query, $offset, $snipStart, $snipEnd);
	}

	/**
	 * Unschedule a campaign that is scheduled to be sent in the future
	 *
	 * @section Campaign  Related
	 * @example mcapi_campaignUnschedule.php
	 * @example xml-rpc_campaignUnschedule.php
	 *
	 * @param string $cid the id of the campaign to unschedule
	 * @return boolean true on success
	 */
	public function campaignUnschedule($cid = null) {
		if (!$cid) {
			$cid = $this->settings['defaultCampaignId'];
		}
		return $this->Mailchimp->campaignUnschedule($cid);
	}

	/**
	 * Schedule a campaign to be sent in the future
	 *
	 * @section Campaign  Related
	 * @example mcapi_campaignSchedule.php
	 * @example xml-rpc_campaignSchedule.php
	 *
	 * @param string $cid the id of the campaign to schedule
	 * @param string $scheduleTime the time to schedule the campaign. For A/B Split "schedule" campaigns, the time for Group A - in YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
	 * @param string $scheduleTimeB optional -the time to schedule Group B of an A/B Split "schedule" campaign - in YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
	 * @return boolean true on success
	 */
	public function campaignSchedule($cid, $scheduleTime, $scheduleTimeB = null) {
		return $this->Mailchimp->campaignSchedule($cid, $scheduleTime, $scheduleTimeB);
	}

	/**
	 * Resume sending an AutoResponder or RSS campaign
	 *
	 * @section Campaign  Related
	 *
	 * @param string $cid the id of the campaign to pause
	 * @return boolean true on success
	 */
	public function campaignResume($cid) {
		return $this->Mailchimp->campaignResume($cid);
	}

	/**
	 * Pause an AutoResponder orRSS campaign from sending
	 *
	 * @section Campaign  Related
	 *
	 * @param string $cid the id of the campaign to pause
	 * @return boolean true on success
	 */
	public function campaignPause($cid) {
		return $this->Mailchimp->campaignPause($cid);
	}

	/**
	 * Send a given campaign immediately. For RSS campaigns, this will "start" them.
	 *
	 * @section Campaign  Related
	 *
	 * @example mcapi_campaignSendNow.php
	 * @example xml-rpc_campaignSendNow.php
	 *
	 * @param string $cid the id of the campaign to send
	 * @return boolean true on success
	 */
	public function campaignSendNow($cid) {
		return $this->Mailchimp->campaignSendNow($cid);
	}

	/**
	 * Send a test of this campaign to the provided email address
	 *
	 * @param array $testEmails an array of email address to receive the test message
	 * @param string $sendType optional by default (null) both formats are sent - "html" or "text" send just that format
	 * @param string $cid the id of the campaign to test
	 * @return boolean true on success
	 */
	public function campaignSendTest(array $testEmails = array(), $sendType = null, $cid = null) {
		if (!$cid) {
			$cid = $this->settings['defaultCampaignId'];
		}
		return $this->Mailchimp->campaignSendTest($cid, $testEmails, $sendType);
	}

	/**
	 * Allows one to test their segmentation rules before creating a campaign using them
	 *
	 * @section Campaign  Related
	 * @example mcapi_campaignSegmentTest.php
	 * @example xml-rpc_campaignSegmentTest.php
	 *
	 * @param string $listId the list to test segmentation on - get lists using lists()
	 * @param array $options with 2 keys:
	 * @return integer total The total number of subscribers matching your segmentation options
	 */
	public function campaignSegmentTest($listId, array $options) {
		return $this->Mailchimp->campaignSegmentTest($listId, $options);
	}

	/**
	 * Create a new draft campaign to send. You <strong>can not</strong> have more than 32,000 campaigns in your account.
	 *
	 * @section Campaign  Related
	 * @example mcapi_campaignCreate.php
	 * @example xml-rpc_campaignCreate.php
	 * @example xml-rpc_campaignCreateABSplit.php
	 * @example xml-rpc_campaignCreateRss.php
	 *
	 * @param string $type the Campaign Type to create - one of "regular", "plaintext", "absplit", "rss", "trans", "auto"
	 * @param array $options a hash of the standard options for this campaign :     *
	 * @return string the ID for the created campaign
	 */
	public function campaignCreate($type, array $options, $content, array $segmentOpts = null, array $typeOpts = null) {
		return $this->Mailchimp->campaignCreate($type, $options, $content, $segmentOpts, $typeOpts);
	}

	/** Update just about any setting for a campaign that has <em>not</em> been sent. See campaignCreate() for details.
	 *
	 *  Caveats:<br/><ul>
	 *        <li>If you set list_id, all segmentation options will be deleted and must be re-added.</li>
	 *        <li>If you set template_id, you need to follow that up by setting it's 'content'</li>
	 *        <li>If you set segment_opts, you should have tested your options against campaignSegmentTest() as campaignUpdate() will not allow you to set a segment that includes no members.</li></ul>
	 * @section Campaign  Related
	 *
	 * @example mcapi_campaignUpdate.php
	 * @example mcapi_campaignUpdateAB.php
	 * @example xml-rpc_campaignUpdate.php
	 * @example xml-rpc_campaignUpdateAB.php
	 *
	 * @param string $cid the Campaign Id to update
	 * @param string $name the parameter name ( see campaignCreate() ). For items in the <strong>options</strong> array, this will be that parameter's name (subject, from_email, etc.). Additional parameters will be that option name  (content, segment_opts). "type_opts" will be the name of the type - rss, auto, trans, etc.
	 * @param mixed  $value an appropriate value for the parameter ( see campaignCreate() ). For items in the <strong>options</strong> array, this will be that parameter's value. For additional parameters, this is the same value passed to them.
	 * @return boolean true if the update succeeds, otherwise an error will be thrown
	 */
	public function campaignUpdate($cid, $name, $value) {
		return $this->Mailchimp->campaignUpdate($cid, $name, $value);
	}

	/** Replicate a campaign.
	 *
	 * @section Campaign  Related
	 *
	 * @example mcapi_campaignReplicate.php
	 *
	 * @param string $cid the Campaign Id to replicate
	 * @return string the id of the replicated Campaign created, otherwise an error will be thrown
	 */
	public function campaignReplicate($cid) {
		return $this->Mailchimp->campaignReplicate($cid);
	}

	/** Delete a campaign. Seriously, "poof, gone!" - be careful!
	 *
	 * @section Campaign  Related
	 *
	 * @example mcapi_campaignDelete.php
	 *
	 * @param string $cid the Campaign Id to delete
	 * @return boolean true if the delete succeeds, otherwise an error will be thrown
	 */
	public function campaignDelete($cid) {
		return $this->Mailchimp->campaignDelete($cid);
	}

	/**
	 * Given a list and a campaign, get all the relevant campaign statistics (opens, bounces, clicks, etc.)
	 *
	 * @section Campaign  Stats
	 *
	 * @example mcapi_campaignStats.php
	 * @example xml-rpc_campaignStats.php
	 *
	 * @param string $cid the campaign id to pull stats for (can be gathered using campaigns())
	 * @return array struct of the statistics for this campaign
	 * @returnf int syntax_errors Number of email addresses in campaign that had syntactical errors.
	 * @returnf int hard_bounces Number of email addresses in campaign that hard bounced.
	 * @returnf int soft_bounces Number of email addresses in campaign that soft bounced.
	 * @returnf int unsubscribes Number of email addresses in campaign that unsubscribed.
	 * @returnf int abuse_reports Number of email addresses in campaign that reported campaign for abuse.
	 * @returnf int forwards Number of times email was forwarded to a friend.
	 * @returnf int forwards_opens Number of times a forwarded email was opened.
	 * @returnf int opens Number of times the campaign was opened.
	 * @returnf date last_open Date of the last time the email was opened.
	 * @returnf int unique_opens Number of people who opened the campaign.
	 * @returnf int clicks Number of times a link in the campaign was clicked.
	 * @returnf int unique_clicks Number of unique recipient/click pairs for the campaign.
	 * @returnf date last_click Date of the last time a link in the email was clicked.
	 * @returnf int users_who_clicked Number of unique recipients who clicked on a link in the campaign.
	 * @returnf int emails_sent Number of email addresses campaign was sent to.
	 * @returnf array absplit If this was an absplit campaign, stats for the A and B groups will be returned
	 */
	public function campaignStats($cid) {
		return $this->Mailchimp->campaignStats($cid);
	}

	/**
	 * Get an array of the urls being tracked, and their click counts for a given campaign
	 *
	 * @section Campaign  Stats
	 *
	 * @example mcapi_campaignClickStats.php
	 * @example xml-rpc_campaignClickStats.php
	 *
	 * @param string $cid the campaign id to pull stats for (can be gathered using campaigns())
	 * @return struct urls will be keys and contain their associated statistics:
	 * @returnf int clicks Number of times the specific link was clicked
	 * @returnf int unique Number of unique people who clicked on the specific link
	 */
	public function campaignClickStats($cid) {
		return $this->Mailchimp->campaignClickStats($cid);
	}

	/**
	 * Get the top 5 performing email domains for this campaign. Users want more than 5 should use campaign campaignEmailStatsAIM()
	 * or campaignEmailStatsAIMAll() and generate any additional stats they require.
	 *
	 * @section Campaign  Stats
	 *
	 * @example mcapi_campaignEmailDomainPerformance.php
	 *
	 * @param string $cid the campaign id to pull email domain performance for (can be gathered using campaigns())
	 * @return array domains email domains and their associated stats
	 * @returnf string domain Domain name or special "Other" to roll-up stats past 5 domains
	 * @returnf int total_sent Total Email across all domains - this will be the same in every row
	 * @returnf int emails Number of emails sent to this domain
	 * @returnf int bounces Number of bounces
	 * @returnf int opens Number of opens
	 * @returnf int clicks Number of clicks
	 * @returnf int unsubs Number of unsubs
	 * @returnf int delivered Number of deliveries
	 * @returnf int emails_pct Percentage of emails that went to this domain (whole number)
	 * @returnf int bounces_pct Percentage of bounces from this domain (whole number)
	 * @returnf int opens_pct Percentage of opens from this domain (whole number)
	 * @returnf int clicks_pct Percentage of clicks from this domain (whole number)
	 * @returnf int unsubs_pct Percentage of unsubs from this domain (whole number)
	 */
	public function campaignEmailDomainPerformance($cid) {
		return $this->Mailchimp->campaignEmailDomainPerformance($cid);
	}

	/**
	 * Get all email addresses the campaign was successfully sent to (ie, no bounces)
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull members for (can be gathered using campaigns())
	 * @param string $status optional the status to pull - one of 'sent', 'hard' (bounce), or 'soft' (bounce). By default, all records are returned
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array a total of all matching emails and the specific emails for this page
	 * @returnf int total   the total number of members for the campaign and status
	 * @returnf array data  the full campaign member records
	 */
	public function campaignMembers($cid, $status = null, $start = 0, $limit = 1000) {
		return $this->Mailchimp->campaignMembers($cid, $status, $start, $limit);
	}

	/**
	 * <strong>DEPRECATED</strong> Get all email addresses with Hard Bounces for a given campaign
	 *
	 * @deprecated See campaignMembers() for a replacement
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array a total of all hard bounced emails and the specific emails for this page
	 * @returnf int total   the total number of hard bounces for the campaign
	 * @returnf array data  the full email addresses that bounced
	 */
	public function campaignHardBounces($cid, $start = 0, $limit = 1000) {
		return $this->Mailchimp->campaignHardBounces($cid, $start, $limit);
	}

	/**
	 * <strong>DEPRECATED</strong> Get all email addresses with Soft Bounces for a given campaign
	 *
	 * @deprecated See campaignMembers() for a replacement
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array a total of all soft bounced emails and the specific emails for this page
	 * @returnf int total   the total number of soft bounces for the campaign
	 * @returnf array data the full email addresses that bounced
	 */
	public function campaignSoftBounces($cid, $start = 0, $limit = 1000) {
		return $this->Mailchimp->campaignSoftBounces($cid, $start, $limit);
	}

	/**
	 * Get all unsubscribed email addresses for a given campaign
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array email addresses that unsubscribed from this campaign along with reasons, if given
	 * @return array a total of all unsubscribed emails and the specific emails for this page
	 * @returnf int total   the total number of unsubscribes for the campaign
	 * @returnf array data  the full email addresses that unsubscribed
	 */
	public function campaignUnsubscribes($cid, $start = 0, $limit = 1000) {
		return $this->Mailchimp->campaignUnsubscribes($cid, $start, $limit);
	}

	/**
	 * Get all email addresses that complained about a given campaign
	 *
	 * @section Campaign  Stats
	 *
	 * @example mcapi_campaignAbuseReports.php
	 *
	 * @param string $cid the campaign id to pull abuse reports for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 500, upper limit set at 1000
	 * @param string $since optional pull only messages since this time - use YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
	 * @return array reports the abuse reports for this campaign
	 * @returnf string date date/time the abuse report was received and processed
	 * @returnf string email the email address that reported abuse
	 * @returnf string type an internal type generally specifying the orginating mail provider - may not be useful outside of filling report views
	 */
	public function campaignAbuseReports($cid, $since = null, $start = 0, $limit = 500) {
		return $this->Mailchimp->campaignAbuseReports($cid, $since, $start, $limit);
	}

	/**
	 * Retrieve the text presented in our app for how a campaign performed and any advice we may have for you - best
	 * suited for display in customized reports pages. Note: some messages will contain HTML - clean tags as necessary
	 *
	 * @section Campaign  Stats
	 *
	 * @example mcapi_campaignAdvice.php
	 *
	 * @param string $cid the campaign id to pull advice text for (can be gathered using campaigns())
	 * @return array advice on the campaign's performance
	 * @returnf msg the advice message
	 * @returnf type the "type" of the message. one of: negative, positive, or neutral
	 */
	public function campaignAdvice($cid) {
		return $this->Mailchimp->campaignAdvice($cid);
	}

	/**
	 * Retrieve the Google Analytics data we've collected for this campaign. Note, requires Google Analytics Add-on to be installed and configured.
	 *
	 * @section Campaign  Stats
	 *
	 * @example mcapi_campaignAnalytics.php
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @return array analytics we've collected for the passed campaign.
	 * @returnf int visits number of visits
	 * @returnf int pages number of page views
	 * @returnf int new_visits new visits recorded
	 * @returnf int bounces vistors who "bounced" from your site
	 * @returnf double time_on_site the total time visitors spent on your sites
	 * @returnf int goal_conversions number of goals converted
	 * @returnf double goal_value value of conversion in dollars
	 * @returnf double revenue revenue generated by campaign
	 * @returnf int transactions number of transactions tracked
	 * @returnf int ecomm_conversions number Ecommerce transactions tracked
	 * @returnf array goals an array containing goal names and number of conversions
	 */
	public function campaignAnalytics($cid) {
		return $this->Mailchimp->campaignAnalytics($cid);
	}

	/**
	 * Retrieve the countries and number of opens tracked for each. Email address are not returned.
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @return array countries an array of countries where opens occurred
	 * @returnf string code The ISO3166 2 digit country code
	 * @returnf string name A version of the country name, if we have it
	 * @returnf int opens The total number of opens that occurred in the country
	 * @returnf bool region_detail Whether or not a subsequent call to campaignGeoOpensByCountry() will return anything
	 */
	public function campaignGeoOpens($cid) {
		return $this->Mailchimp->campaignGeoOpens($cid);
	}

	/**
	 * Retrieve the regions and number of opens tracked for a certain country. Email address are not returned.
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param string $code An ISO3166 2 digit country code
	 * @return array regions an array of regions within the provided country where opens occurred.
	 * @returnf string code An internal code for the region. When this is blank, it indicates we know the country, but not the region
	 * @returnf string name The name of the region, if we have one. For blank "code" values, this will be "Rest of Country"
	 * @returnf int opens The total number of opens that occurred in the country
	 */
	public function campaignGeoOpensForCountry($cid, $code) {
		return $this->Mailchimp->campaignGeoOpensForCountry($cid, $code);
	}

	/**
	 * Retrieve the tracked eepurl mentions on Twitter
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @return array stats an array containing tweets, retweets, clicks, and referrer related to using the campaign's eepurl
	 * @returnf array twitter various Twitter related stats
	 */
	public function campaignEepUrlStats($cid) {
		return $this->Mailchimp->campaignEepUrlStats($cid);
	}

	/**
	 * Retrieve the most recent full bounce message for a specific email address on the given campaign.
	 * Messages over 30 days old are subject to being removed
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param string $email the email address or unique id of the member to pull a bounce message for.
	 * @return array the full bounce message for this email+campaign along with some extra data.
	 * @returnf string date date/time the bounce was received and processed
	 * @returnf string email the email address that bounced
	 * @returnf string message the entire bounce message received
	 */
	public function campaignBounceMessage($cid, $email) {
		return $this->Mailchimp->campaignBounceMessage($cid, $email);
	}

	/**
	 * Retrieve the full bounce messages for the given campaign. Note that this can return very large amounts
	 * of data depending on how large the campaign was and how much cruft the bounce provider returned. Also,
	 * message over 30 days old are subject to being removed
	 *
	 * @section Campaign  Stats
	 *
	 * @example mcapi_campaignBounceMessages.php
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 25, upper limit set at 50
	 * @param string $since optional pull only messages since this time - use YYYY-MM-DD format in <strong>GMT</strong> (we only store the date, not the time)
	 * @return array bounces the full bounce messages for this campaign
	 * @returnf int total that total number of bounce messages for the campaign
	 * @returnf array data an array containing the data for this page
	 */
	public function campaignBounceMessages($cid, $start = 0, $limit = 25, $since = null) {
		return $this->Mailchimp->campaignBounceMessages($cid, $start, $limit, $since);
	}

	/**
	 * Retrieve the Ecommerce Orders tracked by campaignEcommOrderAdd()
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 100, upper limit set at 500
	 * @param string $since optional pull only messages since this time - use YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
	 * @return array the total matching orders and the specific orders for the requested page
	 * @returnf int total the total matching orders
	 * @returnf array data the actual data for each order being returned
	 */
	public function campaignEcommOrders($cid, $start = 0, $limit = 100, $since = null) {
		return $this->Mailchimp->campaignEcommOrders($cid, $start, $limit, $since);
	}

	/**
	 * Get the URL to a customized <a href="http://eepurl.com/gKmL" target="_blank">VIP Report</a> for the specified campaign and optionally send an email to someone with links to it. Note subsequent calls will overwrite anything already set for the same campign (eg, the password)
	 *
	 * @section Campaign  Related
	 *
	 * @param string $cid the campaign id to share a report for (can be gathered using campaigns())
	 * @param array  $opts optional various parameters which can be used to configure the shared report
	 * @return struct Struct containing details for the shared report
	 * @returnf string title The Title of the Campaign being shared
	 * @returnf string url The URL to the shared report
	 * @returnf string secure_url The URL to the shared report, including the password (good for loading in an IFRAME). For non-secure reports, this will not be returned
	 * @returnf string password If secured, the password for the report, otherwise this field will not be returned
	 */
	public function campaignShareReport($cid, array $opts = array()) {
		return $this->Mailchimp->campaignShareReport($cid, $opts);
	}

	/**
	 * Get the content (both html and text) for a campaign either as it would appear in the campaign archive or as the raw, original content
	 *
	 * @section Campaign  Related
	 *
	 * @param string $cid the campaign id to get content for (can be gathered using campaigns())
	 * @param boolean   $forArchive optional controls whether we return the Archive version (true) or the Raw version (false), defaults to true
	 * @return struct Struct containing all content for the campaign (see Returned Fields for details
	 * @returnf string html The HTML content used for the campgain with merge tags intact
	 * @returnf string text The Text content used for the campgain with merge tags intact
	 */
	public function campaignContent($cid, $forArchive = true) {
		return $this->Mailchimp->campaignContent($cid, $forArchive);
	}

	/**
	 * Get the HTML template content sections for a campaign. Note that this <strong>will</strong> return very jagged, non-standard results based on the template
	 * a campaign is using. You only want to use this if you want to allow editing template sections in your applicaton.
	 *
	 * @section Campaign  Related
	 *
	 * @param string $cid the campaign id to get content for (can be gathered using campaigns())
	 * @return array array containing all content section for the campaign -
	 */
	public function campaignTemplateContent($cid) {
		return $this->Mailchimp->campaignTemplateContent($cid);
	}

	/**
	 * Retrieve the list of email addresses that opened a given campaign with how many times they opened - note: this AIM function is free and does
	 * not actually require the AIM module to be installed
	 *
	 * @section Campaign Report Data
	 *
	 * @param string $cid the campaign id to get opens for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array array containing the total records matched and the specific records for this page
	 * @returnf int total the total number of records matched
	 * @returnf array data the actual opens data, including:
	 */
	public function campaignOpenedAIM($cid, $start = 0, $limit = 1000) {
		return $this->Mailchimp->campaignOpenedAIM($cid, $start, $limit);
	}

	/**
	 * Retrieve the list of email addresses that did not open a given campaign
	 *
	 * @section Campaign Report Data
	 *
	 * @param string $cid the campaign id to get no opens for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array array containing the total records matched and the specific records for this page
	 * @returnf int total the total number of records matched
	 * @returnf array data the email addresses that did not open the campaign
	 */
	public function campaignNotOpenedAIM($cid, $start = 0, $limit = 1000) {
		return $this->Mailchimp->campaignNotOpenedAIM($cid, $start, $limit);
	}

	/**
	 * Return the list of email addresses that clicked on a given url, and how many times they clicked
	 *
	 * @section Campaign Report Data
	 *
	 * @param string $cid the campaign id to get click stats for (can be gathered using campaigns())
	 * @param string $url the URL of the link that was clicked on
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array array containing the total records matched and the specific records for this page
	 * @returnf int total the total number of records matched
	 * @returnf array data the email addresses that did not open the campaign
	 */
	public function campaignClickDetailAIM($cid, $url, $start = 0, $limit = 1000) {
		return $this->Mailchimp->campaignClickDetailAIM($cid, $start, $limit);
	}

	/**
	 * Given a campaign and email address, return the entire click and open history with timestamps, ordered by time
	 *
	 * @section Campaign Report Data
	 *
	 * @param string $cid the campaign id to get stats for (can be gathered using campaigns())
	 * @param array $emailAddress an array of up to 50 email addresses to check OR the email "id" returned from listMemberInfo, Webhooks, and Campaigns. For backwards compatibility, if a string is passed, it will be treated as an array with a single element (will not work with XML-RPC).
	 * @return array an array with the keys listed in Returned Fields below
	 * @returnf int success the number of email address records found
	 * @returnf int error the number of email address records which could not be found
	 * @returnf array data arrays containing the actions (opens and clicks) that the email took, with timestamps
	 */
	public function campaignEmailStatsAIM($cid, $emailAddress) {
		return $this->Mailchimp->campaignEmailStatsAIM($cid, $emailAddress);
	}

	/**
	 * Given a campaign and correct paging limits, return the entire click and open history with timestamps, ordered by time,
	 * for every user a campaign was delivered to.
	 *
	 * @section Campaign Report Data
	 * @example mcapi_campaignEmailStatsAIMAll.php
	 *
	 * @param string $cid the campaign id to get stats for (can be gathered using campaigns())
	 * @param integer $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
	 * @param integer $limit optional for large data sets, the number of results to return - defaults to 100, upper limit set at 1000
	 * @return array Array containing a total record count and data including the actions  (opens and clicks) for each email, with timestamps
	 * @returnf int total the total number of records
	 * @returnf array data each record with their details:
	 */
	public function campaignEmailStatsAIMAll($cid, $start = 0, $limit = 100) {
		$params = array($cid);
		$params["cid"] = $cid;
		$params["start"] = $start;
		$params["limit"] = $limit;
		return $this->Mailchimp->campaignEmailStatsAIMAll($cid, $start, $limit);
	}

	/**
	 * Attach Ecommerce Order Information to a Campaign. This will generall be used by ecommerce package plugins
	 * <a href="/plugins/ecomm360.phtml">that we provide</a> or by 3rd part system developers.
	 * @section Campaign  Related
	 *
	 * @param array $order an array of information pertaining to the order that has completed. Use the following keys:
	 * @return boolean true if the data is saved, otherwise an error is thrown.
	 */
	public function campaignEcommOrderAdd(array $order) {
		return $this->Mailchimp->campaignEcommOrderAdd($order);
	}

}
