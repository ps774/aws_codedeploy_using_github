<?php
include_once('php/db.php');
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
$_SESSION['nav-active'] = $_SERVER['REQUEST_URI'];

?>

<html>
<head>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style>
		.s12{
		    font-size: 10px;
			padding: 0 4px;
		}
	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="/<?php echo $base;?>favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/<?php echo $base;?>favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/<?php echo $base;?>favicon/favicon-16x16.png">
	<link rel="manifest" href="/<?php echo $base;?>favicon/site.webmanifest">
	<link rel="mask-icon" href="/<?php echo $base;?>favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Popsixle Terms of Service</title>
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>
		<!-- <p class="pt-2" style="margin-left:2px;"><b>Back to the good old days.</b></p> -->

					
			<div class="p-3 mb-2 card base-card bg-light border text-white bg-danger" id="error_block" style="display:none;">
			</div>
			
			<div class="p-3 mb-2  card base-card bg-light border text-white bg-success" id="success_block" style="display:none;">
			</div>

			
			<div class="mt-3  card base-card bg-light border p-3">
<h4 class="text-center">Terms of Service</h4>
<p class="s3">Last updated: 6/06/2022<br></p>
<p class="s5"><a name="_bef7u94osacl"></a></p>
<p class="s7"><span class="s8">THIS AGREEMENT CONTAINS A MANDATORY ARBITRATION PROVISION AND WAIVER OF JURY TRIAL. PLEASE READ IT CAREFULLY BEFORE AGREEING.</span></p>
<p class="s7"><span class="s8">Attention Exchange Inc., DBA Popsixle, (hereinafter “Popsixle,” “we” or “us”) provides this website (the “Site”), the storage functionality associated with the Site, and the associated data, Software, information, tools, functionality, updates and similar materials delivered or provided by us (collectively, the “Software”), subject to your agreement to and compliance with the conditions set forth in this Terms of Service agreement (the “Agreement”).</span></p>
<p class="s7"><span class="s8">For the purpose of these Terms of Service, along with any amendments to the same, and wherever the context so requires “You”, “Your” or “User” refer to the person visiting, accessing, browsing through and/or using the Software at any point in time. The term “We”, “Us”, “Our” shall mean and refer to the Software and/or the Company, depending on the context.</span></p>
<p class="s7"><span class="s8">This Agreement sets forth the legally binding terms and conditions governing your access to and use of the Software. By accessing or using the Software or otherwise entering into this Agreement, you are creating a binding contract with us. If you do not agree to these terms and conditions, you may not access or use the Software.</span></p>
<p class="s7"><span class="s8">We may revise or update this Agreement by posting an amended version through the Software and making you aware of the revisions, which may be through posting to the Software or otherwise. Your continued access to or use of the Software following an update to this Agreement (or other acceptance method) is considered acceptance of the updated Agreement. Please refer to the “Last updated” date above to see when this Agreement was last updated.</span></p>
<p class="s7"><span class="s8">1.    License</span></p>
<p class="s7"><span class="s8">As long as you are in compliance with the conditions of this Agreement and all incorporated documents, we hereby grant you a limited, revocable, non-assignable, non-transferrable, non-sublicensable, non-resellable, non-exclusive license to access, receive and use the Software. No rights not explicitly listed are granted.</span></p>
<p class="s7"><span> </span></p>
<p class="s7"><span class="s8">Any portion of the Software may not be reproduced, duplicated, copied, sold, resold, visited, or otherwise exploited for any commercial purpose without express written consent of the Company. You may not frame or utilize framing techniques to enclose any trademark, logo, or other proprietary information (including images, text, page layout, or form) of the Software or of the Company and/or its affiliates without the express written consent of the Company.</span></p>
<p class="s9"><span> </span></p>
<p class="s9"><span class="s10">License to use client data</span></p>
<p class="s9"><span class="s11">You hereby grant to Popsixle a royalty-free, non-exclusive, irrevocable, right and license to access your web page(s) and to access and log (a) any information concerning users’ actions, entries, or activities on your web page(s), (b) any information sent to you by users’ web browsers concerning users’ web activities immediately prior to visiting your web page(s) (e.g., URL information and HTTP header information), and/or (c) any data or other information you provide to Popsixle (collectively “Client Data”) for the purposes of (i) providing you with reports and other functions related to the Software; (ii) analyzing and improving the Software; and/or (iii) compiling aggregate data derived from your use of the Software to compile statistics, metrics, insights and general trend data about the Software for, among other things, Popsixle marketing and promotional purposes. This information will be presented only in aggregate form, and we will not share specific site data that identifies you or your visitors without your permission.</span></p>
<p class="s9"><span class="s11">You represent and warrant that you have all rights, licenses, and consents required to license Client Data to Popsixle on these terms, and further represent and warrant that this license does not infringe the rights of any third party or violate any applicable law or regulation.</span></p>
<p class="s9"><span class="s10">License to use your logo and name</span></p>
<p class="s9"><span class="s11">You hereby agree and allow and grant the right to Popsixle to use your name, logo and/or trademark solely for commercial purposes.</span></p>
<p class="s9"><span> </span></p>
<p class="s7"><span class="s8">2.    Incorporated Terms</span></p>
<p class="s7"><span class="s8">The following additional terms are incorporated into this Agreement as if fully set forth herein:</span></p>
<div>
<span class="s12">● </span><a href="privacy.php"><span class="s13">Privacy Policy</span></a>
</div>
<p class="s7"><span class="s8">3.    Software Overview</span></p>
<p class="s9"><span class="s11">Users can use the Software to track, monitor, log, merge, and share customer data and internet usage on their respective Website. </span></p>
<p class="s7"><span class="s8">4.    Eligibility</span></p>
<p class="s9"><span class="s8">By using the Site, you represent and warrant that: (1) you have the legal capacity and you agree to comply with these Terms of Service; (2) you are not a minor in the jurisdiction in which you reside; (3) you will not access the Site through automated or non-human means, whether through a bot, script, or otherwise; (4) you will not use the Site for any illegal or unauthorized purpose; (5) your use of the Site will not violate any applicable law or regulation; (6) you also represent and warrant that you are not a competitor of Popsixle.</span></p>
<p class="s9"><span class="s8">If you provide any information that is untrue, inaccurate, not current, or incomplete, we have the right to suspend or terminate your account and refuse any and all current or future use of the Site (or any portion thereof).</span></p>
<p class="s7"><span class="s8">Some parts or all of the Software may not be available to the general public, and we may impose eligibility rules from time to time. We reserve the right to amend or eliminate these eligibility requirements at any time.</span></p>
<p class="s7"><span> </span></p>
<p class="s7"><span class="s8">5.    Important Notices</span></p>
<p class="s7"><span class="s8">We do not represent or warrant that access to the Software will be error-free or uninterrupted, or without defect, and we do not guarantee that you will be able to access or use the Software, or its features, at all times. We reserve the right at any time and from time to time to modify or discontinue, temporarily or permanently, the Software, or any part thereof, with or without notice.</span></p>
<p class="s7"><span class="s8">The Software may contain typographical errors or inaccuracies and may not be complete or current. We reserve the right to correct any such errors, inaccuracies or omissions and to change or update information at any time without prior notice.</span></p>
<p class="s7"><span class="s8">6.    User Obligations</span></p>
<p class="s7"><span class="s8">Your access to the Software is conditioned on your compliance with the terms of this Agreement, including but not limited to these rules of conduct.</span></p>
<p class="s7"><span class="s8">You agree that you will not violate any applicable law or regulation in connection with your use of the Software.</span></p>
<p class="s7"><span class="s8">You must keep your user name, password, and any other information needed to login to the Software, if applicable, confidential and secure. We are not responsible for any unauthorized access to your account or profile by others.</span></p>
<p class="s7"><span class="s8">You agree not to distribute, upload, make available or otherwise publish through the Software any data, suggestions, information, ideas, comments, causes, promotions, documents, questions, notes, plans, drawings, proposals, or materials similar thereto (“Submissions”) or graphics, text, information, links, profiles, audio, photos, software, music, sounds, video, comments, messages or tags, or similar materials (collectively “Content”) that:</span></p>
<div class="s14">
<span class="s12">● </span><span class="s8">is unlawful or encourages another to engage in anything unlawful;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">contains a virus or any other similar programs or software which may damage the operation of our or another’s computer;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">violates the rights of any party or infringes upon the patent, trademark, trade secret, copyright, right of privacy or publicity or other intellectual property right of any party; or</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">is libelous, defamatory, pornographic, obscene, lewd, indecent, inappropriate, invasive of privacy or publicity rights, abusing, harassing, threatening or bullying.</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">You further agree that you will not do any of the following:</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">breach, through the Software, any agreements that you enter into with any third parties;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">stalk, harass, injure, or harm another individual through the Software;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">modify, adapt, translate, copy, reverse engineer, decompile or disassemble any portion of the Software;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">interfere with or disrupt the operation of the Software, including restricting or inhibiting any other person from using the Software by means of hacking or defacing;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">transmit to or make available in connection with the Software any denial of Software attack, virus, worm, Trojan horse or other harmful code or activity;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">attempt to probe, scan or test the vulnerability of a system or network of the Software or to breach security or authentication measures without proper authorization;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">take any action that imposes, or may impose, in our sole discretion, an unreasonable or disproportionately large load on our infrastructure;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">harvest or collect the email address, contact information, or any other personal information of other users of the Software;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">use any means to crawl, scrape or collect content from the Software via automated or large group means;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">submit, post or make available false, incomplete or misleading information to the Software, or otherwise provide such information to us;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">register for more than one user account;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">impersonate any other person or business;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">attempt to access or access any portion of the Software that is not public; or</span>
</div>
<div class="s15">
<span class="s12">● </span><span class="s8">attempt to override or override any security measures in place on the Software.</span>
</div>
<p class="s7"><span class="s8">You are not licensed to access any portion of the Software that is not public, and you may not attempt to override any security measures in place on the Software.</span></p>
<p class="s7"><span class="s8">Notwithstanding anything herein to the contrary, we reserve the right, in our sole discretion, to suspend or terminate your access to the Software. Notwithstanding the foregoing, our unlimited right to terminate your access to the Software shall not be limited to violations of these rules of conduct.</span></p>
<p class="s7"><span class="s8">7.    Content Submitted or Made Available to Us</span></p>
<p class="s7"><span class="s8">You are under no obligation to submit anything to us, and unless otherwise noted, we will not claim ownership of any Content. However, in order for us to provide the Software, we need your permission to process, display, reproduce and otherwise use content you make available to us.</span></p>
<p class="s7"><span class="s8">Therefore, if you choose to submit any Content to the Software, or otherwise make available any Content through the Software, you hereby grant to us a perpetual, irrevocable, transferable, sub-licensable, non-exclusive, worldwide, royalty-free license to reproduce, use, modify, display, perform, transmit, distribute, translate and create derivative works from any such Content for purposes of providing the Software, and in accordance with your instructions, including without limitation distributing part or all of the Content in any media format through any media channels.</span></p>
<p class="s7"><span class="s8">By submitting any Content or Submissions to us you hereby agree, warrant and represent that: (i) the Content and Submissions do not contain proprietary or confidential information, and the provision of the Content and Submissions is not a violation of any applicable law or any third-party’s rights; (ii) all such Submissions and Content are complete, accurate and true, (iii) we are not under any confidentiality obligation relating to the Content or Submissions; (iv) we shall be entitled to use or disclose the Content or Submissions in any way, in accordance with this Agreement and our </span><a href="privacy.php"><span class="s8">Privacy Policy</span></a><span class="s8">; and (v) you are not entitled to compensation or attribution from us in exchange for the Submissions or Content.</span></p>
<p class="s7"><span class="s8">You acknowledge that we are under no obligation to maintain the Software, or any information, materials, Submissions, Content or other matter you submit, post or make available to or on the Software. We reserve the right to withhold, remove and or discard any such material at any time.</span></p>
<p class="s7"><span class="s8">8.    Our Intellectual Property</span></p>
<p class="s7"><span class="s8">Our graphics, logos, names, designs, page headers, button icons, scripts, and Software names are our trademarks, trade names and/or trade dress. The “look” and “feel” of the Software (including color combinations, button shapes, layout, design and all other graphical elements) are protected by U.S. copyright and trademark law. All product names, names of Software, trademarks and Software marks (“Marks”) are our property or the property of their respective owners, as indicated. You may not use the Marks or copyrights for any purpose whatsoever other than as permitted by this Agreement.</span></p>
<p class="s7"><span class="s8">You acknowledge that the software used to provide the Software, and all enhancements, updates, upgrades, corrections and modifications to the software, all copyrights, patents, trade secrets, or trademarks or other intellectual property rights protecting or pertaining to any aspect of the software (or any enhancements, corrections or modifications) and any and all documentation therefor, are and shall remain our sole and exclusive property or that of our licensors, as the case may be. This Agreement does not convey title or ownership to you, but instead gives you only the limited rights set forth herein.</span></p>
<p class="s7"><span> </span></p>
<p class="s7"><span class="s8">9.    Data Collection and Use</span></p>
<p class="s7"><span class="s8">You understand and agree that our </span><a href="privacy.php"><span class="s8">Privacy Policy</span></a><span class="s8"> shall govern the collection and use of data obtained by us through your access to and use of the Software.</span></p>
<p class="s7"><span class="s8">10. Payment</span></p>
<p class="s7"><span class="s8">Transactions on the Website/Software are secure and protected. Any information entered by the User when transacting on the Website/Software is encrypted to protect the User against unintentional disclosure to third parties. The User’s credit and debit card information is not received, stored by or retained by the Company / Website/Software in any manner. This information is supplied by the User directly to the relevant payment gateway which is authorized to handle the information provided, and is compliant with the regulations and requirements of various banks and institutions and payment franchisees that it is associated with.</span></p>
<p class="s7"><span class="s8">The following payment options are available on the Website/Software:</span></p>
<p class="s7"><span class="s8">a) Domestic and international credit cards issued by banks and financial institutions that are part of the Visa, Mastercard &amp; Amex Card networks;</span></p>
<p class="s7"><span class="s8">b) Visa &amp; Mastercard Debit cards;</span></p>
<p class="s7"><span class="s8">The Payment shall be initiated at the Service start date and the payment shall be made on a monthly basis. </span></p>
<p class="s7"><span> </span></p>
<p class="s7"><span class="s8">11.    Enforcement and Termination</span></p>
<p class="s7"><span class="s8">We reserve the right to deny all or some portion of the Software to any user, in our sole discretion, at any time. </span></p>
<p class="s7"><span class="s8">All grants of any rights from you to us related to Content, Submissions, or other materials, including but not limited to copyright licenses, shall survive any termination of this Agreement. Further, your representations, defense and indemnification obligations survive any termination of this Agreement.</span></p>
<p class="s9"><span class="s11">This User Agreement is effective unless and until terminated by either you or the Company. You may terminate this User Agreement at any time, provided that you discontinue any further use of the Software. The Company may terminate this User Agreement at any time and may do so immediately without notice, and accordingly deny you access to the Software.</span></p>
<p class="s9"><span class="s11">Such termination will be without any liability to the Company. The Company’s right to any Comments and to be indemnified pursuant to the terms hereof, shall survive any termination of this User Agreement. Any such termination of the User Agreement shall not cancel your obligation to pay for service(s) already ordered from the Software or affect any liability that may have arisen under the User Agreement prior to the date of termination.</span></p>
<p class="s9"><span class="s11">In the event that the User does not renew a subscription then the Company shall provide until the last day of the current monthly billing period. No refunds for partial months will be made.</span></p>
<p class="s9"><span class="s11">Upon termination, software will be removed from the User’s website and data capture will cease to occur.</span></p>
<p class="s7"><span> </span></p>
<p class="s7"><span> </span></p>
<p class="s7"><span> </span></p>
<p class="s7"><span class="s8">12.    Disclaimers and Limitation On Liability</span></p>
<p class="s7"><span class="s8">EXCEPT WHERE NOT PERMITTED BY LAW, YOU AGREE AND ACKNOWLEDGE THAT THE SOFTWARE IS PROVIDED “AS IS” AND “AS AVAILABLE”, WITHOUT ANY WARRANTY OR CONDITION, EXPRESS, IMPLIED OR STATUTORY, AND WE, AND OUR PARENTS, SUBSIDIARIES, OFFICERS, DIRECTORS, SHAREHOLDERS, MEMBERS, MANAGERS, EMPLOYEES AND SUPPLIERS, SPECIFICALLY DISCLAIM ANY IMPLIED WARRANTIES OF TITLE, ACCURACY, SUITABILITY, APPLICABILITY, MERCHANTABILITY, PERFORMANCE, FITNESS FOR A PARTICULAR PURPOSE, NON-INFRINGEMENT OR ANY OTHER WARRANTIES OF ANY KIND IN AND TO THE SOFTWARE. NO ADVICE OR INFORMATION (ORAL OR WRITTEN) OBTAINED BY YOU FROM US SHALL CREATE ANY WARRANTY.</span></p>
<p class="s7"><span class="s8">FURTHER, OPINIONS, ADVICE, STATEMENTS, OFFERS, SUBMISSIONS OR OTHER INFORMATION OR CONTENT MADE AVAILABLE THROUGH THE Software, BUT NOT DIRECTLY BY US, ARE THOSE OF THEIR RESPECTIVE AUTHORS, AND SHOULD NOT BE RELIED UPON. WE HAVE NO CONTROL OVER THE QUALITY, SAFETY, OR LEGALITY OF SUCH CONTENT, AND MAKE NO REPRESENTATIONS ABOUT SUCH CONTENT. THE RESPECTIVE AUTHORS ARE SOLELY RESPONSIBLE FOR SUCH CONTENT. YOU ARE SOLELY RESPONSIBLE FOR ANY DECISIONS THAT YOU MAKE BASED UPON SUCH CONTENT.</span></p>
<p class="s7"><span class="s8">ACCESS TO AND USE OF THE SOFTWARE IS AT YOUR SOLE RISK. WE DO NOT WARRANT THAT YOU WILL BE ABLE TO ACCESS OR USE THE SOFTWARE AT THE TIMES OR LOCATIONS OF YOUR CHOOSING; THAT THE SOFTWARE WILL BE UNINTERRUPTED OR ERROR-FREE; THAT DEFECTS WILL BE CORRECTED; THAT DATA TRANSMISSION OR STORAGE IS SECURE OR THAT THE SOFTWARE IS FREE OF INACCURACIES, MISREPRESENTATIONS, VIRUSES OR OTHER HARMFUL INFORMATION OR COMPONENTS.</span></p>
<p class="s7"><span class="s8">TO THE MAXIMUM EXTENT PERMITTED BY LAW, AND EXCEPT AS PROHIBITED BY LAW, IN NO EVENT SHALL WE OR OUR AFFILIATES, LICENSORS AND BUSINESS PARTNERS (COLLECTIVELY, THE “RELATED PARTIES”) BE LIABLE TO YOU BASED ON OR RELATED TO THE SOFTWARE WHETHER BASED IN CONTRACT, TORT (INCLUDING NEGLIGENCE), STRICT LIABILITY OR OTHERWISE, AND SHALL NOT BE RESPONSIBLE FOR ANY LOSSES OR DAMAGES, INCLUDING WITHOUT LIMITATION DIRECT, INDIRECT, INCIDENTAL, CONSEQUENTIAL, OR SPECIAL DAMAGES ARISING OUT OF OR IN ANY WAY CONNECTED WITH ACCESS TO OR USE OF THE SOFTWARE.</span></p>
<p class="s7"><span class="s8">Notwithstanding the foregoing, in the event that a court shall find that the above disclaimers are not enforceable, then, to the maximum extent permissible by law, you agree that neither we nor any of our subsidiaries, affiliated companies, employees, members, shareholders, officers or directors shall be liable for (1) any damages in excess of the greater of (a) $500.00 or (b) the amounts paid to, or by, you through the Software within the last six months, or (2) any indirect, incidental, punitive, exemplary, special, or consequential damages or loss of use, lost revenue, lost profits or data to you or any third party from your access to or use of the Software. This limitation shall apply regardless of the basis of your claim, whether other provisions of this Agreement have been breached, or whether or not the limited remedies provided herein fail of their essential purpose.</span></p>
<p class="s7"><span class="s8">This limitation shall not apply to any damage that we cause you intentionally and knowingly in violation of this Agreement or applicable law that cannot be disclaimed in this Agreement.</span></p>
<p class="s7"><span class="s8">SOME STATES MAY NOT PERMIT CERTAIN DISCLAIMERS AND LIMITATIONS, AND ANY SUCH DISCLAIMERS OR LIMITATIONS ARE VOID WHERE PROHIBITED.</span></p>
<p class="s7"><span class="s8">13.    Indemnification</span></p>
<p class="s7"><span class="s8">You agree to defend, indemnify and hold us and all Brands, and each of our affiliates, subsidiaries, suppliers, licensors, and licensees, and each of their officers, directors, shareholders, members, employees and agents harmless from all allegations, judgments, awards, losses, liabilities, costs and expenses, including but not limited to reasonable attorney’s fees, expert witness fees, and costs of litigation arising out of or based on (i) Submissions or Content you submit, post or upload to, or transmit through the Software, (ii) your access to or use of the Software, (iii) your violation of this Agreement, (iv) any tax obligations that arise from your access to or use of the Software, and (v) any conduct, activity or action which is unlawful or illegal under any state, federal or common law, or is violative of the rights of any individual or entity, engaged in, caused by, or facilitated in any way through the access to or use of the Software.</span></p>
<p class="s7"><span class="s8">14.    Governing Law and Jurisdiction; Arbitration</span></p>
<p class="s7"><span class="s8">You agree that any claim or dispute arising out of or relating in any way to the Software will be resolved solely and exclusively by binding arbitration, rather than in court, except that you may assert claims in small claims court if your claims qualify. The Federal Arbitration Act and federal arbitration law apply to this agreement. The laws of the Commonwealth of Massachusetts shall govern this Agreement, and shall be used in any arbitration proceeding.</span></p>
<p class="s7"><span class="s8">There is no judge or jury in arbitration, and court review of an arbitration award is limited. However, an arbitrator can award on an individual basis the same damages and relief as a court (including injunctive and declaratory relief or statutory damages), and must follow the terms of this Agreement as a court would.</span></p>
<p class="s7"><span class="s8">To begin an arbitration proceeding, you must send a letter requesting arbitration and describing your claim to the following address: Legal Department, Attention Exchange Inc., 251 Little Falls Drive, Wilmington, DE 19808.</span></p>
<p class="s7"><span class="s8">Arbitration under this Agreement will be conducted by the American Arbitration Association (AAA) under its rules then in effect, shall be conducted in English, and shall be located in Boston, Massachusetts. Payment of all filing, administration and arbitrator fees will be governed by the AAA's rules.</span></p>
<p class="s7"><span class="s8">You and Popsixle agree that any dispute resolution proceedings will be conducted only on an individual basis and not in a class, consolidated or representative action. If for any reason a claim proceeds in court rather than in arbitration, both you and Popsixle agree that each have waived any right to a jury trial.</span></p>
<p class="s7"><span class="s8">Notwithstanding the foregoing, you agree that we may bring suit in court to enjoin infringement or other misuse of intellectual property or other proprietary rights.</span></p>
<p class="s7"><span class="s8">All aspects of the arbitration proceeding, and any ruling, decision or award by the arbitrator, will be strictly confidential for the benefit of all parties.</span></p>
<p class="s7"><span class="s8">To the extent arbitration does not apply, you agree that any dispute arising out of or relating to the Software, or to us, may only be brought by you in a state or federal court located in Boston, Massachusetts. YOU HEREBY WAIVE ANY OBJECTION TO THIS VENUE AS INCONVENIENT OR INAPPROPRIATE, AND AGREE TO EXCLUSIVE JURISDICTION AND VENUE IN MASSACHUSETTS.</span></p>
<p class="s7"><span class="s8">15.    Policies for Children</span></p>
<p class="s7"><span class="s8">The Software is not directed to individuals under the age of 13. In the event that we discover that a child under the age of 13 has provided personally identifiable information to us, we will make efforts to delete the child’s information if required by the Children's Online Privacy Protection Act. Please see the Federal Trade Commission's website for (www.ftc.gov) for more information.</span></p>
<p class="s7"><span class="s8">Notwithstanding the foregoing, pursuant to 47 U.S.C. Section 230 (d), as amended, we hereby notify you that parental control protections are commercially available to assist you in limiting access to material that is harmful to minors. More information on the availability of such software can be found through publicly available sources. You may wish to contact your internet Software provider for more information.</span></p>
<p class="s7"><span class="s8">16.    General</span></p>
<p class="s7"><span class="s8">Severability. If any provision of this Agreement is found for any reason to be unlawful, void or unenforceable, then that provision will be given its maximum enforceable effect, or shall be deemed severable from this Agreement and will not affect the validity and enforceability of any remaining provision.</span></p>
<p class="s7"><span class="s8">Revisions. This Agreement is subject to change on a prospective basis at any time. In the event that we change this Agreement, you may be required to re-affirm the Agreement through use of the Software or otherwise. Your access to and use of the Software after the effective date of any changes will constitute your acceptance of such changes.</span></p>
<p class="s7"><span class="s8">Assignment. We may assign our rights under this Agreement, in whole or in part, to any person or entity at any time with or without your consent. You may not assign the Agreement without our prior written consent, and any unauthorized assignment by you shall be null and void.</span></p>
<p class="s7"><span class="s8">No Waiver. Our failure to enforce any provision of this Agreement shall in no way be construed to be a present or future waiver of such provision, nor in any way affect the right of any party to enforce each and every such provision thereafter. The express waiver by us of any provision, condition or requirement of this Agreement shall not constitute a waiver of any future obligation to comply with such provision, condition or requirement.</span></p>
<p class="s7"><span class="s8">Notices. All notices given by you or required under this Agreement shall be in writing and addressed to: Legal Department, Attention Exchange Inc., 251 Little Falls Drive, Wilmington, DE 19808.</span></p>
<p class="s7"><span class="s8">Equitable Remedies. You hereby agree that we would be irreparably damaged if the terms of this Agreement were not specifically enforced, and therefore you agree that we shall be entitled, without bond, other security, or proof of damages, to appropriate equitable remedies with respect to breaches of this Agreement, in addition to such other remedies as we may otherwise have available to us under applicable laws.</span></p>
<p class="s7"><span class="s8">Force Majeure. In no event shall we or our affiliates be liable to you for any damage, delay, or failure to perform resulting directly or indirectly from a force majeure event.</span></p>
<p class="s7"><span class="s8">Entire Agreement. This Agreement, including the documents expressly incorporated by reference, constitutes the entire agreement between you and us with respect to the Software, and supersedes all prior or contemporaneous communications, whether electronic, oral or written.</span></p>
<p class="s7"><span class="s8">17.    Copyright Policy</span></p>
<p class="s7"><span class="s8">If you believe in good faith that any material posted on our Software infringes the copyright in your work, please contact our copyright agent, designated under the Digital Millennium Copyright Act (“DMCA”) (17 U.S.C. §512(c)(3)), with correspondence containing the following:</span></p>
<div class="s14">
<span class="s12">● </span><span class="s8">A physical or electronic signature of the owner, or a person authorized to act on behalf of the owner, of the copyright that is allegedly infringed;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">Identification of the copyrighted work claimed to have been infringed;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">Identification, with information reasonably sufficient to allow its location of the material that is claimed to be infringing;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">Information reasonably sufficient to permit us to contact you;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law; and,</span>
</div>
<div class="s15">
<span class="s12">● </span><span class="s8">A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</span>
</div>
<p class="s7"><span class="s8">You acknowledge that if you fail to comply with all of the requirements of this policy, your DMCA notice may not be valid. For any questions regarding this procedure, or to submit a complaint, please contact our designated DMCA Copyright Agent:</span></p>
<p class="s7"><span class="s8">   Copyright Agent</span></p>
<p class="s7"><span class="s8">   Attention Exchange Inc.</span></p>
<p class="s7"><span class="s8">   251 Little Falls Drive</span></p>
<p class="s7"><span class="s8">   Wilmington, DE 19808</span></p>
<p class="s7"><span class="s8">   Phone: 781-547-1880</span></p>
<p class="s7"><span class="s8">   E-mail: team@popsixle.com</span></p>
<p class="s7"><span class="s8">18.    Complaint Policy (Including Trademark and Privacy)</span></p>
<p class="s7"><span class="s8">If you believe in good faith that any material posted on the Software infringes any of your rights other than in copyright, or is otherwise unlawful, you must send a notice to team@popsixle.com containing the following information:</span></p>
<div class="s14">
<span class="s12">● </span><span class="s8">Your name, physical address, e-mail address and phone number;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">A description of the material posted on the Site that you believe violates your rights or is otherwise unlawful, and which parts of said materials you believe should be remedied or removed</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">Identification of the location of the material on the Site;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">If you believe that the material violates your rights, a statement as to the basis of the rights that you claim are violated;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">If you believe that the material is unlawful or violates the rights of others, a statement as to the basis of this belief;</span>
</div>
<div class="s14">
<span class="s12">● </span><span class="s8">A statement under penalty of perjury that you have a good faith belief that use of the material in the manner complained of is not authorized and that the information you are providing is accurate to the best of your knowledge and in good faith; and</span>
</div>
<div class="s15">
<span class="s12">● </span><span class="s8">Your physical or electronic signature.</span>
</div>
<p class="s7"> </p>
<p class="s7"><span class="s8">If we receive a message that complies with all of these requirements, we will evaluate the submission, and if appropriate, in our sole discretion, we will take action. We may disclose your submission to the poster of the claimed violative material, or any other party.</span></p>
<p><span> </span></p>	

			</div>
		</div>

	</body>
</html>
