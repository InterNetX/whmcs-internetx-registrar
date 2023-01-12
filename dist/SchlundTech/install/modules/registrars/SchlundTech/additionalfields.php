<?php

// .au
$additionaldomainfields[".com.au"] = array();
$additionaldomainfields[".com.au"][] = array("Name" => "Registrant ID", "LangVar" => "autldregid", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true,);
$additionaldomainfields[".com.au"][] = array("Name" => "Registrant ID Type", "LangVar" => "autldregidtype", "Type" => "dropdown", "Options" => "ABN,ACN,TAS,VIC,NSW,ACT,QLD,NT,WA,SA,OTHERS", "Default" => "ABN",);
$additionaldomainfields[".com.au"][] = array("Name" => "Eligibility Type", "LangVar" => "autldeligtype", "Type" => "dropdown", "Options" => "Company,Registered Business,Sole Trader,Partnership,Trademark Owner,Pending TM Owner,Citizen/Resident,Incorporated Association,Unincorporated Association,Club,Non-profit Organisation,Charity,Trade Union,Industry Body,Commercial Statutory Body,Political Party,Religious/Church Groups,Registrable Body,Indigenous Corporation,Registered Organisation,Cooperative,Trust,Educational Institution,Commonwealth entity,Statutory Body,Trading Cooperative,Company Limited by Guarantee,Non-distributing Cooperative,Non-trading cooperative,Charitable Trust,Public/Private Ancillary Fund,Peak State/Territory Body,Not-for-profit community group,OTHER", "Default" => "Company",);
$additionaldomainfields[".com.au"][] = array("Name" => "Eligibility Name", "LangVar" => "autldeligname", "Type" => "text", "Size" => "30", "Default" => "", "Required" => false,);
$additionaldomainfields[".com.au"][] = array("Name" => "Eligibility ID", "LangVar" => "autldeligid", "Type" => "text", "Size" => "30", "Default" => "", "Required" => false,);
$additionaldomainfields[".com.au"][] = array("Name" => "Eligibility ID Type", "LangVar" => "autldeligidtype", "Type" => "dropdown", "Options" => "ACN,ABN,VIC,NSW,SA,NT,WA,TAS,ACT,QLD,TM,OTHER", "Default" => "", "Required" => true,);
$additionaldomainfields[".com.au"][] = array("Name" => "Eligibility Reason", "LangVar" => "autldeligreason", "Type" => "radio", "Options" => "Domain name is an Exact Match Abbreviation or Acronym of your Entity or Trading Name.,Close and substantial connection between the domain name and the operations of your Entity.", "Default" => "Domain name is an Exact Match Abbreviation or Acronym of your Entity or Trading Name.",);

$additionaldomainfields[".net.au"] = $additionaldomainfields[".com.au"];
$additionaldomainfields[".org.au"] = $additionaldomainfields[".com.au"];
$additionaldomainfields[".asn.au"] = $additionaldomainfields[".com.au"];
$additionaldomainfields[".id.au"] = $additionaldomainfields[".com.au"];

// .aero
$additionaldomainfields[".aero"] = array();
$additionaldomainfields[".aero"][] = array("Name" => ".aero ID Number", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true,);
$additionaldomainfields[".aero"][] = array("Name" => ".aero Password", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true,);


// .nu
$additionaldomainfields[".nu"] = array();
$additionaldomainfields[".nu"][] = array("DisplayName" => "VAT Number", "Name" => "vatnumber", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".nu"][] = array("DisplayName" => "Identification Number", "Name" => "idnumber", "Type" => "text", "Size" => "30", "Default" => "");

//// .es
// Remove Additional Fields from standard whmcs
$additionaldomainfields[".es"][] = array("Name" => "ID Form Type", "Remove" => true);
$additionaldomainfields[".es"][] = array("Name" => "ID Form Number", "Remove" => true,);
$additionaldomainfields[".es"][] = array("Name" => "Legal Form", "Remove" => true,);
$additionaldomainfields[".es"][] = array("DisplayName" => "VAT Number", "Name" => "vatnumber", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".es"][] = array("DisplayName" => "Identification Number", "Name" => "idnumber", "Type" => "text", "Size" => "30", "Default" => "");


//// .fr
$additionaldomainfields[".fr"] = $additionaldomainfields[".nu"];

// .cat
$additionaldomainfields[".cat"] = array();
$additionaldomainfields[".cat"][] = array("Name" => "ENS Auth ID", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".cat"][] = array("Name" => "ENS Key", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".cat"][] = array("Name" => "Intended Use", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true,);

// .hk
$additionaldomainfields[".hk"]   = array();
$additionaldomainfields[".hk"][] = array("Name" => "Individual Document Type", "Type" => "dropdown", "Options" => "HKID|Honk Kong Identity Card,OTHID|Other Countries Region Identity Card,PASSNO|Passport Number,IRTHCERT|Birth Certificate,OTHIDV|Other Individual Document", "Default" => "");
$additionaldomainfields[".hk"][] = array("Name" => "Specify the Organization Type", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".hk"][] = array("Name" => "Document Number", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true);
$additionaldomainfields[".hk"][] = array("Name" => "Country Origin Country", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".hk"][] = array("Name" => "Are you Adult", "DisplayName" => "Are you 18  years of age or older?", "Type" => "radio", "Options" => "yes|Yes,no|No",);
$additionaldomainfields[".hk"][] = array("Name" => "Industry Type", "DisplayName" => "Industry Type", "Type" => "dropdown", "Options" => "0|Not Selected,010100|Plastics&#44; Petro-Chemicals&#44; Chemicals - Plastics and Plastic Products,010200|Plastics&#44; Petro-Chemicals&#44; Chemicals - Rubber and Rubber Products,010300|Plastics&#44; Petro-Chemicals&#44; Chemicals - Fibre Materials and Products,010400|Plastics&#44; Petro-Chemicals&#44; Chemicals - Petroleum&#44; Coal and Other Fuels,010500|Plastics&#44; Petro-Chemicals&#44; Chemicals - Chemicals and Chemical Products,020100|Metals&#44; Machinery&#44; Equipment - Metal Materials and Treatment,020200|Metals&#44; Machinery&#44; Equipment - Metal Products,020300|Metals&#44; Machinery&#44; Equipment - Industrial Machinery and Supplies,020400|Metals&#44; Machinery&#44; Equipment - Precision and Optical Equipment,020500|Metals&#44; Machinery&#44; Equipment - Moulds and Dies,030100|Printing&#44; Paper&#44; Publishing - Printing&#44; Photocopying&#44; Publishing,030200|Printing&#44; Paper&#44; Publishing - Paper&#44; Paper Products,040100|Construction&#44; Decoration&#44; Environmental Engineering - Construction Contractors,040200|Construction&#44; Decoration&#44; Environmental Engineering - Construction Materials,040300|Construction&#44; Decoration&#44; Environmental Engineering - Decoration Materials,040400|Construction&#44; Decoration&#44; Environmental Engineering - Construction&#44; Safety Equipment and Supplies,040500|Construction&#44; Decoration&#44; Environmental Engineering - Decoration&#44; Locksmiths&#44; Plumbing and Electrical Works,040600|Construction&#44; Decoration&#44; Environmental Engineering - Fire Protection Equipment and Services,040700|Construction&#44; Decoration&#44; Environmental Engineering - Environmental Engineering&#44; Waste Reduction,050100|Textiles&#44; Clothing and Accessories - Textiles&#44; Fabric,050200|Textiles&#44; Clothing and Accessories - Clothing,050300|Textiles&#44; Clothing and Accessories - Uniforms&#44; Special Clothing,050400|Textiles&#44; Clothing and Accessories - Clothing Manufacturing Accessories,050500|Textiles&#44; Clothing and Accessories - Clothing Processing and Equipment,050600|Textiles&#44; Clothing and Accessories - Fur&#44; Leather and Leather Goods,050700|Textiles&#44; Clothing and Accessories - Handbags&#44; Footwear&#44; Optical Goods&#44; Personal Accessories,060100|Electronics&#44; Electrical Appliances - Electronic Equipment and Supplies,060200|Electronics&#44; Electrical Appliances - Electronic Parts and Components,060300|Electronics&#44; Electrical Appliances - Electrical Appliances&#44; Audio-Visual Equipment,070100|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Kitchenware&#44; Tableware,070200|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Bedding,070300|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Bathroom&#44; Cleaning Accessories,070400|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Household Goods,070500|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Wooden&#44; Bamboo and Rattan Goods,070600|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Home Furnishings&#44; Arts and Crafts,070700|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Watches&#44; Clocks,070800|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Jewellery Accessories,070900|Houseware&#44; Watches&#44; Clocks&#44; Jewellery&#44; Toys&#44; Gifts - Toys&#44; Games&#44; Gifts,080100|Business and Professional Services&#44; Finance - Accounting&#44; Legal Services,080200|Business and Professional Services&#44; Finance - Advertising&#44; Promotion Services,080300|Business and Professional Services&#44; Finance - Consultancy Services,080400|Business and Professional Services&#44; Finance - Translation&#44; Design Services,080500|Business and Professional Services&#44; Finance - Cleaning&#44; Pest Control Services,080600|Business and Professional Services&#44; Finance - Security Services,080700|Business and Professional Services&#44; Finance - Trading&#44; Business Services,080800|Business and Professional Services&#44; Finance - Employment Services,080900|Business and Professional Services&#44; Finance - Banking&#44; Finance&#44; Investment,081000|Business and Professional Services&#44; Finance - Insurance,081100|Business and Professional Services&#44; Finance - Property&#44; Real Estate,090100|Transportation&#44; Logistics - Land Transport&#44; Motorcars,090200|Transportation&#44; Logistics - Sea Transport&#44; Boats,090300|Transportation&#44; Logistics - Air Transport,090400|Transportation&#44; Logistics - Moving&#44; Warehousing&#44; Courier and Logistics Services,090500|Transportation&#44; Logistics - Freight Forwarding,100100|Office Equipment&#44; Furniture&#44; Stationery&#44; Information Technology - Office&#44; Commercial Equipment and Supplies,100200|Office Equipment&#44; Furniture&#44; Stationery&#44; Information Technology - Office and Home Furniture,100300|Office Equipment&#44; Furniture&#44; Stationery&#44; Information Technology - Stationery and Educational Supplies,100400|Office Equipment&#44; Furniture&#44; Stationery&#44; Information Technology - Telecommunication Equipment and Services,100500|Office Equipment&#44; Furniture&#44; Stationery&#44; Information Technology - Computers&#44; Information Technology,110100|Food&#44; Flowers&#44; Fishing and Agriculture - Food Products and Supplies,110200|Food&#44; Flowers&#44; Fishing and Agriculture - Beverages&#44; Tobacco,110300|Food&#44; Flowers&#44; Fishing and Agriculture - Restaurant Equipment and Supplies,110400|Food&#44; Flowers&#44; Fishing and Agriculture - Flowers&#44; Artificial Flowers&#44; Plants,110500|Food&#44; Flowers&#44; Fishing and Agriculture - Fishing,110600|Food&#44; Flowers&#44; Fishing and Agriculture - Agriculture,120100|Medical Services&#44; Beauty&#44; Social Services - Medicine and Herbal Products,120200|Medical Services&#44; Beauty&#44; Social Services - Medical and Therapeutic Services,120300|Medical Services&#44; Beauty&#44; Social Services - Medical Equipment and Supplies,120400|Medical Services&#44; Beauty&#44; Social Services - Beauty&#44; Health,120500|Medical Services&#44; Beauty&#44; Social Services - Personal Services,120600|Medical Services&#44; Beauty&#44; Social Services - Organizations&#44; Associations,120700|Medical Services&#44; Beauty&#44; Social Services - Information&#44; Media,120800|Medical Services&#44; Beauty&#44; Social Services - Public Utilities,120900|Medical Services&#44; Beauty&#44; Social Services - Religion&#44; Astrology&#44; Funeral Services,130100|Culture&#44; Education - Music&#44; Arts,130200|Culture&#44; Education - Learning Instruction and Training,130300|Culture&#44; Education - Elementary Education,130400|Culture&#44; Education - Tertiary Education&#44; Other Education Services,130500|Culture&#44; Education - Sporting Goods,130600|Culture&#44; Education - Sporting&#44; Recreational Facilities and Venues,130700|Culture&#44; Education - Hobbies&#44; Recreational Activities,130800|Culture&#44; Education - Pets&#44; Pets Services and Supplies,140101|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Restaurant Guide - Chinese,140102|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Restaurant Guide - Asian,140103|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Restaurant Guide - Western,140200|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Catering Services&#44; Eateries,140300|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Entertainment Venues,140400|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Entertainment Production and Services,140500|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Entertainment Equipment and Facilities,140600|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Shopping Venues,140700|Dining&#44; Entertainment&#44; Shopping&#44; Travel - Travel&#44; Hotels and Accommodation",);

// .IT
// First remove setup from dist
$additionaldomainfields[".it"][] = array("Name" => "Legal Type", "Remove" => true);
$additionaldomainfields[".it"][] = array("Name" => "Tax ID", "Remove" => true);
$additionaldomainfields[".it"][] = array("Name" => "Publish Personal Data", "Remove" => true);
$additionaldomainfields[".it"][] = array("Name" => "Accept Section 3 of .IT registrar contract", "Remove" => true);
$additionaldomainfields[".it"][] = array("Name" => "Accept Section 5 of .IT registrar contract", "Remove" => true);
$additionaldomainfields[".it"][] = array("Name" => "Accept Section 6 of .IT registrar contract", "Remove" => true);
$additionaldomainfields[".it"][] = array("Name" => "Accept Section 7 of .IT registrar contract", "Remove" => true);

// Our Custom fields
$additionaldomainfields[".it"][] = array("Name" => "IT Entity Type", "Type" => "dropdown", "Options" => "1|Italian and foreign natural Persons,7|Foreign non-natural persons,2|Italian Companies/One Man Companies,3|Italian Freelance Workers/Professionals,4|Italian Non-Profit Organizations,5|Italian public Organizations,6|Italian other Subjects", "Default" => "");
$additionaldomainfields[".it"][] = array("Name" => "ID Number", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true);
$additionaldomainfields[".it"][] = array("DisplayName" => "VAT Number", "Name" => "vatnumber", "Type" => "text", "Size" => "30", "Default" => "");

$additionaldomainfields[".it"][] = array(
    "Name"    => "<a href= /resources/domains/TLDfiles/it_regulation_assignation_v7.1.pdf target=_blank>Accept Regulation of the assignment and management of domain names</a>",
    "LangVar" => "ittldsec3", "Type"    => "tickbox", "Required" => true
);
$additionaldomainfields[".it"][] = array(
    "Name"    => "<a href= /resources/domains/TLDfiles/whois-policy_it.pdf target=_blank>Accept Whois policy (italian language only)</a>",
    "LangVar" => "ittldsec3", "Type"    => "tickbox", "Required" => true
);
$additionaldomainfields[".it"][] = array(
    "Name"    => "<a href= /resources/domains/TLDfiles/it_dispute_resolution_v2.1.pdf target=_blank>Accept Dispute resolution in the cctld .it</a>",
    "LangVar" => "ittldsec3", "Type"    => "tickbox", "Required" => true
);
$additionaldomainfields[".it"][] = array(
    "Name"    => "<a href= /resources/domains/TLDfiles/it_dispute_resolution_guidelines_v3.1.pdf target=_blank>Accept Guidelines for the resolution of disputes in the cctld .it</a>",
    "LangVar" => "ittldsec3", "Type"    => "tickbox", "Required" => true
);

// .jobs
$additionaldomainfields[".jobs"]   = array();
$additionaldomainfields[".jobs"][] = array("Name" => "Position", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".jobs"][] = array("Name" => "Website", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".jobs"][] = array("Name" => "Industry Class", "Type" => "dropdown", "Options" => "2|Accounting/Banking/Finance,3|Agriculture/Farming,21|Biotechnology/Science,5|Computer/Information Technology,4|Construction/Building Services,12|Consulting,6|Education/Training/Library,7|Entertainment,13|Environmental,19|Hospitality,10|Government/Civil Service,11|Healthcare,15|HR/Recruiting,16|Insurance,17|Legal,18|Manufacturing,20|Media/Advertising,9|Parks & Recreation,26|Pharmaceutical,22|Real Estate,14|Restaurant/Food Service,23|Retail,8|Telemarketing,24|Transportation,25|Other", "Default" => "");
$additionaldomainfields[".jobs"][] = array("Name" => "HR Member", "Type" => "radio", "Options" => "1|Yes,0|No",);
$additionaldomainfields[".jobs"][] = array("Name" => "Administrative Contact", "Type" => "radio", "Options" => "1|Yes,0|No",);

// .pro
//$additionaldomainfields[".pro"]   = array();
//$additionaldomainfields[".pro"][] = array("Name" => "Profession", "Type" => "text", "Size" => "30", "Default" => "");
//$additionaldomainfields[".pro"][] = array("Name" => "Authority Name", "Type" => "text", "Size" => "30", "Default" => "");
//$additionaldomainfields[".pro"][] = array("Name" => "Authority URL", "Type" => "text", "Size" => "30", "Default" => "");
//$additionaldomainfields[".pro"][] = array("Name" => "License Number", "Type" => "text", "Size" => "30", "Default" => "");

// .ro
// remove dist
$additionaldomainfields['.ro'][] = array('Name'    => 'CNPFiscalCode', "Remove" => true);
$additionaldomainfields['.ro'][] = array('Name'    => 'Registration Number', "Remove" => true);
$additionaldomainfields['.ro'][] = array('Name'    => 'Registrant Type', "Remove" => true);

// ADD Custom
$additionaldomainfields[".ro"][] = array("Name" => "RO Person Type", "Type" => "dropdown", "Options" => "p|Private Person,ap|Authorized Person,nc|Non-Commercial Organization,c|Commercial,gi|Government Institute,pi|Public Institute,o|Other Juridical Persons", "Default" => "");
$additionaldomainfields[".ro"][] = array("Name" => "ID Card Number", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true);
$additionaldomainfields[".ro"][] = array("Name" => "Company Number", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true);

// Copy for other tlds
$additionaldomainfields['.arts.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.co.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.com.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.firm.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.info.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.nom.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.nt.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.org.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.rec.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.ro.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.store.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.tm.ro'] = $additionaldomainfields['.ro'];
$additionaldomainfields['.www.ro'] = $additionaldomainfields['.ro'];

// .ru
$additionaldomainfields[".ru"]   = array();
$additionaldomainfields[".ru"][] = array("Name" => "ID Card Number", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".ru"][] = array("Name" => "ID authority", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".ru"][] = array("Name" => "Date of Issue", "Type" => "text", "Size" => "30", "Default" => "", "Description" => "DD.MM.YYYY");
$additionaldomainfields[".ru"][] = array("Name" => "Validation Data", "Type" => "text", "Size" => "30", "Default" => "", "Description" => "DD.MM.YYYY");

//.co.uk
// First remove setup from dist
$additionaldomainfields[".co.uk"][] = array("Name" => "Legal Type", "Remove" => true);
$additionaldomainfields[".co.uk"][] = array("Name" => "Registrant Name", "Remove" => true);
$additionaldomainfields[".co.uk"][] = array("Name" => "WHOIS Opt-out", "Remove" => true);
$additionaldomainfields['.co.uk'][] = ['Name' => 'Company ID Number', "Remove" => true];
// Our Custom fields
$additionaldomainfields[".co.uk"][] = array("Name" => "UK Type", "Type" => "dropdown", "Options" => "LTD|UK Limited Company,PLC|UK Public Limited Company,
PTNR|UK Partnership,STRA|UK Sole Trader,LLP|UK Limited Liability Partnership,IP|UK Industrial/Provident Registered Company,IND|UK Individual (representing self),SCH|UK School,
RCHAR|UK Registered Charity,GOV|UK Government Body,CRC|UK Corporation by Royal Charter,
STAT|UK Statutory Body,OTHER|UK Entity that does not fit into any of the above (e.g. clubs&#44; associations&#44; many universities),FIND|Non-UK Individual (representing self),FCORP|Non-UK Corporation,FOTHER|Non-UK Entity that does not fit into any of the above (e.g. charities&#44; schools&#44; clubs&#44; associations)", "Default" => "", "Required" => true);
$additionaldomainfields[".co.uk"][] = array("Name" => "ID Number for individuals", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".co.uk"][] = array("Name" => "Company or ID Number", "Type" => "text", "Size" => "30", "Default" => "");
$additionaldomainfields[".uk"] = $additionaldomainfields[".co.uk"];
$additionaldomainfields[".net.uk"] = $additionaldomainfields[".co.uk"];
$additionaldomainfields[".org.uk"] = $additionaldomainfields[".co.uk"];
$additionaldomainfields[".me.uk"] = $additionaldomainfields[".co.uk"];
$additionaldomainfields[".plc.uk"] = $additionaldomainfields[".co.uk"];
$additionaldomainfields[".ltd.uk"] = $additionaldomainfields[".co.uk"];

//.de
// Remove Additional Fields from standard whmcs
$additionaldomainfields[".de"][] = array("Name" => "Tax ID", "Remove" => true);
$additionaldomainfields[".de"][] = array("Name" => "Address Confirmation", "Remove" => true,);
$additionaldomainfields[".de"][] = array("Name" => "Agree to DE Terms", "Remove" => true,);

// .CA
$additionaldomainfields[".ca"]   = array();
$additionaldomainfields[".ca"][] = array("Name" => "Legal Type", "LangVar" => "catldlegaltype", "Type" => "dropdown", "Options" => "Corporation,Canadian Citizen,Permanent Resident of Canada,Government,Canadian Educational Institution,Canadian Unincorporated Association,Canadian Hospital,Partnership Registered in Canada,Trade-mark registered in Canada,Canadian Trade Union,Canadian Political Party,Canadian Library Archive or Museum,Trust established in Canada,Aboriginal Peoples,Legal Representative of a Canadian Citizen,Official mark registered in Canada", "Default" => "Corporation", "Description" => "Legal type of registrant contact",);
$additionaldomainfields[".ca"][] = array("Name" => "CIRA Agreement", "LangVar" => "catldciraagreement", "Type" => "tickbox", "Description" => "Tick to confirm you agree to the CIRA Registration Agreement shown below<br /><blockquote>You have read, understood and agree to the terms and conditions of the Registrant Agreement, and that CIRA may, from time to time and at its discretion, amend any or all of the terms and conditions of the Registrant Agreement, as CIRA deems appropriate, by posting a notice of the changes on the CIRA website and by sending a notice of any material changes to Registrant. You meet all the requirements of the Registrant Agreement to be a Registrant, to apply for the registration of a Domain Name Registration, and to hold and maintain a Domain Name Registration, including without limitation CIRA's Canadian Presence Requirements for Registrants, at: www.cira.ca/assets/Documents/Legal/Registrants/CPR.pdf. CIRA will collect, use and disclose your personal information, as set out in CIRA's Privacy Policy, at: www.cira.ca/assets/Documents/Legal/Registrants/privacy.pdf</blockquote>",);

// .travel
$additionaldomainfields[".travel"]   = array();
$additionaldomainfields[".travel"][] = array("Name" => "Travel Unique Identifying Number", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true);


//.swiss
// Remove additional fields from standard whmcs
$additionaldomainfields[".swiss"][] = array("Name" => "Core Intended Use", "Remove" => true);
$additionaldomainfields[".swiss"][] = array("Name" => "Registrant Enterprise ID", "Remove" => true);

// Add our custom fields
$additionaldomainfields[".swiss"][] = array("Name" => ".swiss Enterprise ID", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true);
$additionaldomainfields[".swiss"][] = array("Name" => ".swiss Intended Use", "Type" => "text", "Size" => "30", "Default" => "", "Required" => true);
