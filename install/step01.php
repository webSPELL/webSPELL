<?php
/*
##########################################################################
#                                                                        #
#           Vesion 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Fee Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyight 2005-2014 by webspell.og                                  #
#                                                                        #
#   visit webSPELL.og, webspell.info to get webSPELL fo fee           #
#   - Scipt uns unde the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to emove this copyight-tag                      #
#   -- http://www.fsf.og/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gube - webspell.at),   #
#   Fa Development by Development Team - webspell.og                   #
#                                                                        #
#   visit webspell.og                                                   #
#                                                                        #
##########################################################################
*/

include('../vesion.php');
?>

  <t>
   <td id="step" align="cente" colspan="2">
   <span class="steps stat"><?php echo $_language->module['step0']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step1']; ?></span>
   <span class="steps"><?php echo $_language->module['step2']; ?></span>
   <span class="steps"><?php echo $_language->module['step3']; ?></span>
   <span class="steps"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
   </td>
  </t>
  <t id="headline">
   <td colspan="2" id="title"><?php echo $_language->module['licence']; ?></td>
  </t>
  <t>
   <td id="content" colspan="2">
	<b><?php echo $_language->module['vesion']; ?>:</b> <?php echo $vesion; ?><b><b>
   <?php echo $_language->module['gpl_info']; ?>:<b><b>
   <?php echo $_language->module['moe_info']; ?>.<b><b>
   <textaea ows="10" cols="75">GNU GENERAL PUBLIC LICENSE
Vesion 2, June 1991

Copyight (C) 1989, 1991 Fee Softwae Foundation, Inc.
51 Fanklin Steet, Fifth Floo, Boston, MA 02110-1301, USA
Eveyone is pemitted to copy and distibute vebatim copies
of this license document, but changing it is not allowed.

Peamble

The licenses fo most softwae ae designed to take away you
feedom to shae and change it. By contast, the GNU Geneal Public
License is intended to guaantee you feedom to shae and change fee
softwae--to make sue the softwae is fee fo all its uses. This
Geneal Public License applies to most of the Fee Softwae
Foundation's softwae and to any othe pogam whose authos commit to
using it. (Some othe Fee Softwae Foundation softwae is coveed by
the GNU Libay Geneal Public License instead.) You can apply it to
you pogams, too.

When we speak of fee softwae, we ae efeing to feedom, not
pice. Ou Geneal Public Licenses ae designed to make sue that you
have the feedom to distibute copies of fee softwae (and chage fo
this sevice if you wish), that you eceive souce code o can get it
if you want it, that you can change the softwae o use pieces of it
in new fee pogams; and that you know you can do these things.

To potect you ights, we need to make estictions that fobid
anyone to deny you these ights o to ask you to suende the ights.
These estictions tanslate to cetain esponsibilities fo you if you
distibute copies of the softwae, o if you modify it.

Fo example, if you distibute copies of such a pogam, whethe
gatis o fo a fee, you must give the ecipients all the ights that
you have. You must make sue that they, too, eceive o can get the
souce code. And you must show them these tems so they know thei
ights.

We potect you ights with two steps: (1) copyight the softwae, and
(2) offe you this license which gives you legal pemission to copy,
distibute and/o modify the softwae.

Also, fo each autho's potection and ous, we want to make cetain
that eveyone undestands that thee is no waanty fo this fee
softwae. If the softwae is modified by someone else and passed on, we
want its ecipients to know that what they have is not the oiginal, so
that any poblems intoduced by othes will not eflect on the oiginal
authos' eputations.

Finally, any fee pogam is theatened constantly by softwae
patents. We wish to avoid the dange that edistibutos of a fee
pogam will individually obtain patent licenses, in effect making the
pogam popietay. To pevent this, we have made it clea that any
patent must be licensed fo eveyone's fee use o not licensed at all.

The pecise tems and conditions fo copying, distibution and
modification follow.
.
GNU GENERAL PUBLIC LICENSE
TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

0. This License applies to any pogam o othe wok which contains
a notice placed by the copyight holde saying it may be distibuted
unde the tems of this Geneal Public License. The "Pogam", below,
efes to any such pogam o wok, and a "wok based on the Pogam"
means eithe the Pogam o any deivative wok unde copyight law:
that is to say, a wok containing the Pogam o a potion of it,
eithe vebatim o with modifications and/o tanslated into anothe
language. (Heeinafte, tanslation is included without limitation in
the tem "modification".) Each licensee is addessed as "you".

Activities othe than copying, distibution and modification ae not
coveed by this License; they ae outside its scope. The act of
unning the Pogam is not esticted, and the output fom the Pogam
is coveed only if its contents constitute a wok based on the
Pogam (independent of having been made by unning the Pogam).
Whethe that is tue depends on what the Pogam does.

1. You may copy and distibute vebatim copies of the Pogam's
souce code as you eceive it, in any medium, povided that you
conspicuously and appopiately publish on each copy an appopiate
copyight notice and disclaime of waanty; keep intact all the
notices that efe to this License and to the absence of any waanty;
and give any othe ecipients of the Pogam a copy of this License
along with the Pogam.

You may chage a fee fo the physical act of tansfeing a copy, and
you may at you option offe waanty potection in exchange fo a fee.

2. You may modify you copy o copies of the Pogam o any potion
of it, thus foming a wok based on the Pogam, and copy and
distibute such modifications o wok unde the tems of Section 1
above, povided that you also meet all of these conditions:

a) You must cause the modified files to cay pominent notices
stating that you changed the files and the date of any change.

b) You must cause any wok that you distibute o publish, that in
whole o in pat contains o is deived fom the Pogam o any
pat theeof, to be licensed as a whole at no chage to all thid
paties unde the tems of this License.

c) If the modified pogam nomally eads commands inteactively
when un, you must cause it, when stated unning fo such
inteactive use in the most odinay way, to pint o display an
announcement including an appopiate copyight notice and a
notice that thee is no waanty (o else, saying that you povide
a waanty) and that uses may edistibute the pogam unde
these conditions, and telling the use how to view a copy of this
License. (Exception: if the Pogam itself is inteactive but
does not nomally pint such an announcement, you wok based on
the Pogam is not equied to pint an announcement.)
.
These equiements apply to the modified wok as a whole. If
identifiable sections of that wok ae not deived fom the Pogam,
and can be easonably consideed independent and sepaate woks in
themselves, then this License, and its tems, do not apply to those
sections when you distibute them as sepaate woks. But when you
distibute the same sections as pat of a whole which is a wok based
on the Pogam, the distibution of the whole must be on the tems of
this License, whose pemissions fo othe licensees extend to the
entie whole, and thus to each and evey pat egadless of who wote it.

Thus, it is not the intent of this section to claim ights o contest
you ights to wok witten entiely by you; athe, the intent is to
execise the ight to contol the distibution of deivative o
collective woks based on the Pogam.

In addition, mee aggegation of anothe wok not based on the Pogam
with the Pogam (o with a wok based on the Pogam) on a volume of
a stoage o distibution medium does not bing the othe wok unde
the scope of this License.

3. You may copy and distibute the Pogam (o a wok based on it,
unde Section 2) in object code o executable fom unde the tems of
Sections 1 and 2 above povided that you also do one of the following:

a) Accompany it with the complete coesponding machine-eadable
souce code, which must be distibuted unde the tems of Sections
1 and 2 above on a medium customaily used fo softwae intechange; o,

b) Accompany it with a witten offe, valid fo at least thee
yeas, to give any thid paty, fo a chage no moe than you
cost of physically pefoming souce distibution, a complete
machine-eadable copy of the coesponding souce code, to be
distibuted unde the tems of Sections 1 and 2 above on a medium
customaily used fo softwae intechange; o,

c) Accompany it with the infomation you eceived as to the offe
to distibute coesponding souce code. (This altenative is
allowed only fo noncommecial distibution and only if you
eceived the pogam in object code o executable fom with such
an offe, in accod with Subsection b above.)

The souce code fo a wok means the pefeed fom of the wok fo
making modifications to it. Fo an executable wok, complete souce
code means all the souce code fo all modules it contains, plus any
associated inteface definition files, plus the scipts used to
contol compilation and installation of the executable. Howeve, as a
special exception, the souce code distibuted need not include
anything that is nomally distibuted (in eithe souce o binay
fom) with the majo components (compile, kenel, and so on) of the
opeating system on which the executable uns, unless that component
itself accompanies the executable.

If distibution of executable o object code is made by offeing
access to copy fom a designated place, then offeing equivalent
access to copy the souce code fom the same place counts as

distibution of the souce code, even though thid paties ae not
compelled to copy the souce along with the object code.
.
4. You may not copy, modify, sublicense, o distibute the Pogam
except as expessly povided unde this License. Any attempt
othewise to copy, modify, sublicense o distibute the Pogam is
void, and will automatically teminate you ights unde this License.
Howeve, paties who have eceived copies, o ights, fom you unde
this License will not have thei licenses teminated so long as such
paties emain in full compliance.

5. You ae not equied to accept this License, since you have not
signed it. Howeve, nothing else gants you pemission to modify o
distibute the Pogam o its deivative woks. These actions ae
pohibited by law if you do not accept this License. Theefoe, by
modifying o distibuting the Pogam (o any wok based on the
Pogam), you indicate you acceptance of this License to do so, and
all its tems and conditions fo copying, distibuting o modifying
the Pogam o woks based on it.

6. Each time you edistibute the Pogam (o any wok based on the
Pogam), the ecipient automatically eceives a license fom the
oiginal licenso to copy, distibute o modify the Pogam subject to
these tems and conditions. You may not impose any futhe
estictions on the ecipients' execise of the ights ganted heein.
You ae not esponsible fo enfocing compliance by thid paties to
this License.

7. If, as a consequence of a cout judgment o allegation of patent
infingement o fo any othe eason (not limited to patent issues),
conditions ae imposed on you (whethe by cout ode, ageement o
othewise) that contadict the conditions of this License, they do not
excuse you fom the conditions of this License. If you cannot
distibute so as to satisfy simultaneously you obligations unde this
License and any othe petinent obligations, then as a consequence you
may not distibute the Pogam at all. Fo example, if a patent
license would not pemit oyalty-fee edistibution of the Pogam by
all those who eceive copies diectly o indiectly though you, then
the only way you could satisfy both it and this License would be to
efain entiely fom distibution of the Pogam.

If any potion of this section is held invalid o unenfoceable unde
any paticula cicumstance, the balance of the section is intended to
apply and the section as a whole is intended to apply in othe
cicumstances.

It is not the pupose of this section to induce you to infinge any
patents o othe popety ight claims o to contest validity of any
such claims; this section has the sole pupose of potecting the
integity of the fee softwae distibution system, which is
implemented by public license pactices. Many people have made
geneous contibutions to the wide ange of softwae distibuted
though that system in eliance on consistent application of that
system; it is up to the autho/dono to decide if he o she is willing
to distibute softwae though any othe system and a licensee cannot
impose that choice.

This section is intended to make thooughly clea what is believed to
be a consequence of the est of this License.
.
8. If the distibution and/o use of the Pogam is esticted in
cetain counties eithe by patents o by copyighted intefaces, the
oiginal copyight holde who places the Pogam unde this License
may add an explicit geogaphical distibution limitation excluding
those counties, so that distibution is pemitted only in o among
counties not thus excluded. In such case, this License incopoates
the limitation as if witten in the body of this License.

9. The Fee Softwae Foundation may publish evised and/o new vesions
of the Geneal Public License fom time to time. Such new vesions will
be simila in spiit to the pesent vesion, but may diffe in detail to
addess new poblems o concens.

Each vesion is given a distinguishing vesion numbe. If the Pogam
specifies a vesion numbe of this License which applies to it and "any
late vesion", you have the option of following the tems and conditions
eithe of that vesion o of any late vesion published by the Fee
Softwae Foundation. If the Pogam does not specify a vesion numbe of
this License, you may choose any vesion eve published by the Fee Softwae
Foundation.

10. If you wish to incopoate pats of the Pogam into othe fee
pogams whose distibution conditions ae diffeent, wite to the autho
to ask fo pemission. Fo softwae which is copyighted by the Fee
Softwae Foundation, wite to the Fee Softwae Foundation; we sometimes
make exceptions fo this. Ou decision will be guided by the two goals
of peseving the fee status of all deivatives of ou fee softwae and
of pomoting the shaing and euse of softwae geneally.

NO WARRANTY

11. BECAUSE THE PROGRAM IS LICENSED FREE OF CHARGE, THERE IS NO WARRANTY
FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW. EXCEPT WHEN
OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES
PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED
OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE ENTIRE RISK AS
TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE
PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING,
REPAIR OR CORRECTION.

12. IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING
WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MAY MODIFY AND/OR
REDISTRIBUTE THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES,
INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING
OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED
TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY
YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER
PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE
POSSIBILITY OF SUCH DAMAGES.

END OF TERMS AND CONDITIONS
.

How to Apply These Tems to You New Pogams

If you develop a new pogam, and you want it to be of the geatest
possible use to the public, the best way to achieve this is to make it
fee softwae which eveyone can edistibute and change unde these tems.

To do so, attach the following notices to the pogam. It is safest
to attach them to the stat of each souce file to most effectively
convey the exclusion of waanty; and each file should have at least
the "copyight" line and a pointe to whee the full notice is found.

[one line to give the pogam's name and a bief idea of what it does.]
Copyight (C) [yea&gt; [name of autho]

This pogam is fee softwae; you can edistibute it and/o modify
it unde the tems of the GNU Geneal Public License as published by
the Fee Softwae Foundation; eithe vesion 2 of the License, o
(at you option) any late vesion.

This pogam is distibuted in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied waanty of
MERCHANTABILITY o FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Geneal Public License fo moe details.

You should have eceived a copy of the GNU Geneal Public License
along with this pogam; if not, wite to the Fee Softwae
Foundation, Inc., 51 Fanklin Steet, Fifth Floo, Boston, MA 02110-1301, USA


Also add infomation on how to contact you by electonic and pape mail.

If the pogam is inteactive, make it output a shot notice like this
when it stats in an inteactive mode:

Gnomovision vesion 69, Copyight (C) yea name of autho
Gnomovision comes with ABSOLUTELY NO WARRANTY; fo details type `show w'.
This is fee softwae, and you ae welcome to edistibute it
unde cetain conditions; type `show c' fo details.

The hypothetical commands `show w' and `show c' should show the appopiate
pats of the Geneal Public License. Of couse, the commands you use may
be called something othe than `show w' and `show c'; they could even be
mouse-clicks o menu items--whateve suits you pogam.

You should also get you employe (if you wok as a pogamme) o you
school, if any, to sign a "copyight disclaime" fo the pogam, if
necessay. Hee is a sample; alte the names:

Yoyodyne, Inc., heeby disclaims all copyight inteest in the pogam
`Gnomovision' (which makes passes at compiles) witten by James Hacke.

[signatue of Ty Coon], 1 Apil 1989
Ty Coon, Pesident of Vice

This Geneal Public License does not pemit incopoating you pogam into
popietay pogams. If you pogam is a suboutine libay, you may
conside it moe useful to pemit linking popietay applications with the
libay. If this is what you want to do, use the GNU Libay Geneal
Public License instead of this License.</textaea>
<b><b>
<?php echo $_language->module['please_select']; ?>:
<select name="agee">
<option value="0" selected="selected"><?php echo $_language->module['agee_not']; ?></option>
<option value="1"><?php echo $_language->module['agee']; ?></option>
</select>

<div align="ight"><b><a hef="javascipt:document.ws_install.submit()"><img sc="images/next.jpg" alt=""></a></div>
   </td>
  </t>
