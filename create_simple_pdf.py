from reportlab.lib.pagesizes import letter
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak
from reportlab.lib.enums import TA_JUSTIFY, TA_CENTER
from reportlab.lib import colors

pdf_file = 'c:/wamp64/www/checkout/Stuck_in_18th_Century.pdf'
doc = SimpleDocTemplate(pdf_file, pagesize=letter, topMargin=0.5*inch, bottomMargin=0.5*inch, leftMargin=0.75*inch, rightMargin=0.75*inch)

styles = getSampleStyleSheet()

title_style = ParagraphStyle(
    'Title',
    parent=styles['Heading1'],
    fontSize=42,
    textColor=colors.HexColor('#1B3A5C'),
    spaceAfter=30,
    alignment=TA_CENTER,
    fontName='Helvetica-Bold'
)

subtitle_style = ParagraphStyle(
    'Subtitle',
    parent=styles['Heading2'],
    fontSize=14,
    textColor=colors.HexColor('#2E86AB'),
    spaceAfter=25,
    alignment=TA_CENTER,
    fontName='Helvetica'
)

body_style = ParagraphStyle(
    'Body',
    parent=styles['BodyText'],
    fontSize=11,
    textColor=colors.HexColor('#2D3A4A'),
    spaceAfter=12,
    alignment=TA_JUSTIFY,
    leading=16
)

info_style = ParagraphStyle(
    'Info',
    parent=styles['Normal'],
    fontSize=10,
    textColor=colors.HexColor('#5A6E82'),
    alignment=TA_CENTER,
    spaceAfter=6
)

divider_style = ParagraphStyle(
    'Divider',
    parent=styles['Normal'],
    fontSize=10,
    textColor=colors.HexColor('#2E86AB'),
    alignment=TA_CENTER,
    spaceAfter=20
)

elements = []

# Title Page - Professional Design
elements.append(Spacer(1, 1.2*inch))
elements.append(Paragraph('Stuck in 18th Century', title_style))
elements.append(Paragraph('━' * 50, divider_style))
elements.append(Spacer(1, 0.4*inch))
elements.append(Paragraph('Koli Akter', info_style))
elements.append(Spacer(1, 0.1*inch))
elements.append(Paragraph('Student ID: 056', info_style))
elements.append(Spacer(1, 0.2*inch))
elements.append(Paragraph('─' * 60, divider_style))
elements.append(Spacer(1, 0.2*inch))
elements.append(Paragraph('ENG 3102', subtitle_style))
elements.append(Paragraph('18th Century English Literature', subtitle_style))
elements.append(PageBreak())

# Introduction
elements.append(Paragraph(
    'The 18th century is often called the Age of Enlightenment, a period when people began to rely more on science, logic, and reason rather than tradition or blind belief. It was a time of questioning—people started asking why things were the way they were and searched for answers through knowledge and observation. This revolutionary shift in human consciousness fundamentally altered how individuals approached understanding the world around them. Thinkers and scholars challenged long-held beliefs that had been accepted for centuries, demanding evidence and rational explanation rather than accepting authority without question. The intellectual fervor of this era sparked movements that would reshape society, politics, philosophy, and culture for generations to come. The Enlightenment represented a profound belief in the power of human reason and the capacity of individuals to improve their condition through education and critical thinking.',
    body_style
))

# Today's World
elements.append(Paragraph(
    'Today, we live in a completely different world, shaped by digital technology, the internet, and advanced science. However, even with all this progress, a question still remains: are we truly different from people of the 18th century, or are we simply a modern version of them? On the surface, the differences appear staggering. We communicate instantly across continents, access vast repositories of information with a few keystrokes, and possess scientific understanding that would have seemed miraculous to our ancestors. We have conquered diseases, explored space, and created artificial intelligence that can perform tasks once thought to be exclusively human domains. Yet beneath these technological marvels and scientific achievements, human nature itself has remained surprisingly constant. Our fundamental drives, desires, and behavioral patterns echo those of centuries past, suggesting that while our tools have evolved dramatically, we ourselves have evolved far more slowly.',
    body_style
))

# Surface Changes
elements.append(Paragraph(
    'At first glance, it seems like we have changed a lot. Our world is faster, more connected, and more technologically advanced. We use smartphones, social media, artificial intelligence, and digital platforms in our daily lives. Information is available instantly, and communication happens across the globe within seconds. In this sense, we have clearly moved far beyond the 18th century. The pace of life has accelerated dramatically. What once took months to accomplish through letters and travel now occurs in milliseconds through digital transmission. We can watch events unfold on the other side of the world as they happen. We can conduct business, maintain relationships, and pursue education without ever leaving our homes. The sheer volume of information available to us is incomprehensible when compared to what was accessible to even the most privileged scholars of the 18th century. Yet this abundance of information and speed of communication have not necessarily made us wiser or more thoughtful. Instead, they have sometimes contributed to a world that moves so quickly that we have little time for the kind of deep reflection and careful consideration that characterized Enlightenment thinking.',
    body_style
))

# Deeper Similarities
elements.append(Paragraph(
    'But if we look deeper, the similarities become clearer. Like people of the Enlightenment, we strongly believe in science and rational thinking. We trust doctors, scientists, and technology. During global crises, such as pandemics, people turn to science for solutions. This demonstrates that our faith in rational thought and scientific inquiry remains as strong today as it was three centuries ago. When faced with the COVID-19 pandemic, societies turned to epidemiologists, virologists, and researchers for guidance. People waited anxiously for vaccine development, placing their hopes in the scientific method and the cumulative knowledge of the global medical community. This reliance on empirical evidence and systematic investigation shows that the core belief of the Enlightenment—that reason and observation are our best tools for understanding reality—persists deeply in modern consciousness. Education is also highly valued, just as it was during the Enlightenment. Students are encouraged to think critically, analyze information, and form logical arguments. This shows that the foundation built in the 18th century is still very much alive today. Universities remain bastions of intellectual inquiry, and societies continue to invest heavily in educational systems with the belief that an educated populace is essential for progress and enlightenment. Parents sacrifice much to provide their children with quality education, seeing it as the pathway to better futures.',
    body_style
))

# The Other Side of Enlightenment
elements.append(Paragraph(
    'However, the Enlightenment period was not only about knowledge and reason. It was also a time of social competition, pride, and personal image. People cared deeply about how they were seen by others. Social status, fashion, and reputation played a huge role in everyday life. In many ways, society was built on appearance and public perception. The aristocracy displayed their wealth and importance through elaborate clothing, grand estates, and carefully orchestrated social events. A nobleman would not simply own fine things; these possessions had to be visible, admired, and envied by others. The intricate codes of behavior, dress, and speech that governed social interactions were as much about maintaining and signaling one\'s position as they were about genuine courtesy. Even those of modest means aspired to climb the social hierarchy, and they used whatever means available to them to present themselves as more important or successful than they actually were. Fashion trends, manners, and cultural tastes became tools for social competition. The salons of Paris and London, while centers of intellectual discourse, were simultaneously stages for social performance where people competed for status and influence. This darker side of the Enlightenment reveals that beneath the noble ideals of reason and progress lay the very human desires for status, recognition, and superiority.',
    body_style
))

# Modern Digital World
elements.append(Paragraph(
    'This is where the comparison with today becomes very interesting. In the modern digital world, especially on platforms like Instagram, people present carefully designed versions of their lives. Photos are edited, moments are selected, and captions are crafted to create a certain image. People often show their happiest, most successful, or most attractive sides, while hiding their struggles and imperfections. This creates a gap between reality and representation. The mechanics of social media have made this performance of identity both easier and more consequential than ever before. Someone can spend hours curating their online presence, selecting photos that present them in the best light, writing captions that convey wit or wisdom, and strategically timing posts for maximum engagement. The tools available—filters, editing software, carefully chosen angles—allow for unprecedented control over one\'s public image. What would have taken hours of preparation for an 18th-century social gathering can now be accomplished and shared globally in minutes. Yet the fundamental impulse remains unchanged: we want others to see us in a positive light, to admire us, and to think well of us. The platform has simply become more sophisticated and the audience has expanded from a local community to potentially millions of people worldwide.',
    body_style
))

# The Perfect Day Illusion
elements.append(Paragraph(
    'For example, a person might post a picture of a perfect day out, smiling and looking confident. But what we do not see is the stress, the bad moments, or the ordinary parts of their life. This selective sharing creates an illusion, and others who see it may start comparing their own lives to this unrealistic standard. The photograph captures a single moment, carefully selected and edited to present an idealized version of reality. But it reveals nothing of the difficult conversation that happened an hour before the photo was taken, or the financial worries that plague the person\'s thoughts, or the health issues they are struggling with quietly. The viewers of this image, living their own complicated and messy lives, begin to feel inadequate when they compare their everyday reality—complete with boredom, conflicts, and failures—to this curated highlight reel. They do not realize that everyone they follow is doing the same thing: showing only the best, hiding the rest. This creates a collective delusion where the average person believes they are the only one struggling, the only one whose life is not picture-perfect, the only one who fails and suffers. The result is widespread anxiety, low self-esteem, and a persistent feeling of inadequacy that affects mental health and well-being.',
    body_style
))

# Old Habit
elements.append(Paragraph(
    'This behavior is not new—it is simply a modern version of an old habit. In the 18th century, people showed their status through clothing, manners, and social gatherings. Today, people do the same thing through posts, followers, and likes. The platform has changed, but the intention remains the same. Humans still want to be admired, respected, and noticed. A wealthy merchant in the 1700s would wear fine silks imported from abroad, display valuable art in his home, and host lavish dinner parties to demonstrate his success and importance. A modern entrepreneur achieves similar goals by posting photos of luxury vacations, expensive purchases, and exclusive events. The wealthy of the 18th century proved their intellectual standing by maintaining a library and participating in scholarly discussions; today\'s intellectuals display their knowledge through carefully composed posts about books, ideas, and personal development. The mechanisms of status display have evolved, but the underlying psychology has not. We still use visible markers to communicate our position in the social hierarchy. We still believe that what we own, what we do, and how we present ourselves to others determines our worth and our place in society. The only difference is that the audience has changed from a local community to a global one, and the performance has become constant rather than limited to special occasions.',
    body_style
))

# Validation
elements.append(Paragraph(
    'Another important issue is validation. In the past, validation came from society—people wanted approval from their community or social circle. Today, validation comes in the form of likes, comments, and shares. A simple post can become a source of happiness or disappointment depending on how others react to it. This shows how deeply social approval still affects us. An 18th-century person derived their sense of worth from their standing in their community, the respect they received from peers, and the approval of those whose opinions mattered most to them. A favorable mention in society could elevate one\'s status for months, while a scandal could destroy a reputation. Today, this need for approval has been quantified and made immediately visible. A person posts something hoping for likes and comments. When the post receives many responses, they feel validated and happy. If it receives few interactions, they feel disappointed and hurt. A simple numerical rating has become a proxy for their value as a person. The immediacy of this feedback creates an addictive cycle: post, wait for response, feel validated or hurt, post again. This digital validation system operates on the same psychological principles as gambling—intermittent rewards that keep people engaged and seeking more. The impact on mental health, particularly for young people, is significant and increasingly documented by researchers.',
    body_style
))

# Amplified Pressure
elements.append(Paragraph(
    'At the same time, the digital world has made this pressure even stronger. In the 18th century, social comparison was limited to a small group of people. Now, through social media, we compare ourselves with hundreds or even thousands of others. This can create anxiety, low self-esteem, and a constant feeling of not being "good enough." An 18th-century artisan might compare themselves to the best craftsmen in their city, perhaps a dozen or so highly skilled individuals. Today, someone with the same skills can compare themselves to the best craftspeople from around the world. A teenager in a small town can compare their appearance to thousands of professionally photographed and edited images of people considered beautiful by global standards. A young professional can measure their career progress against peers from every continent, many of whom present curated versions of their greatest achievements. This exponential expansion of the comparison group creates a psychological environment where most people will always feel inadequate because there will always be someone smarter, more attractive, more successful, or more talented. In previous eras, most people had a relatively fixed social position based on birth and local circumstances. Today, because we can see and compare ourselves to such a vast array of people, we have developed what psychologists call "social comparison anxiety." The result is a society experiencing unprecedented levels of stress, depression, and anxiety related to perceived failure and inadequacy.',
    body_style
))

# Misuse of Knowledge
elements.append(Paragraph(
    'Even though we have access to more knowledge than ever before, we do not always use it wisely. The Enlightenment thinkers believed that reason would lead to progress and a better society. However, today we often see the opposite. Misinformation spreads quickly online, people follow trends without thinking, and sometimes emotions are valued more than facts. We have access to more information than any generation in history, yet we may actually be less informed. Algorithms designed to maximize engagement prioritize sensational, emotionally provocative content over accurate, nuanced information. False information spreads faster and further than corrections. People live in information ecosystems tailored to their existing beliefs, making it difficult to encounter perspectives that challenge their views. Someone who believes a particular conspiracy theory can find thousands of websites, videos, and social media posts that reinforce that belief. The original Enlightenment dream was that access to information would naturally lead to better reasoning and wiser decisions. But this dream did not account for human psychology and economics. We are not purely rational beings who automatically seek and accept truth. We are emotional, tribal creatures who prefer information that confirms what we already believe, that makes us feel part of a community, and that gives us a sense of agency and understanding, even if that understanding is false. The business model of social media platforms amplifies these tendencies because engagement—not truth—is what generates profit.',
    body_style
))

# Following Without Thinking
elements.append(Paragraph(
    'For example, many people share information on social media without checking if it is true. Others follow popular opinions just to fit in, rather than forming their own views. This shows that although we have the tools for critical thinking, we do not always apply them. The explanation for this apparent contradiction lies in the difference between capability and motivation. Having access to tools for research does not mean people will use them. The barriers to critical thinking are not only intellectual but also psychological and social. It takes effort and time to verify information, and most people are busy with immediate concerns. It is easier and psychologically more comfortable to accept information that aligns with what we already think. Furthermore, social pressures strongly influence what we believe and share. If everyone in your social circle believes something, going against that consensus feels risky and isolating. People often adopt opinions not because they have thoroughly investigated them, but because they want to belong to a group or because they trust someone they like. This pattern of behavior is not new. In the 18th century, people also followed fashionable opinions and trusted those in their social circle. The difference is that now this happens at global scale and at digital speed, meaning that false information and poorly thought-out ideas can spread to millions of people before anyone bothers to fact-check them.',
    body_style
))

# Technology and Identity
elements.append(Paragraph(
    'Another interesting point is how technology affects our identity. In the digital world, people can create a completely different version of themselves. They can choose what to show and what to hide. Over time, this can make it difficult to separate real identity from online identity. Some people develop entirely different personas for different platforms or contexts. They might be professional and formal on LinkedIn, casual and humorous on Facebook, and edgy or provocative on Twitter or TikTok. In multiplayer online games or virtual worlds, people might adopt a completely different identity, sometimes with a different gender, race, or personality. This ability to construct and reconstruct identity is unprecedented in human history. In previous eras, a person was largely defined by unchangeable characteristics like birth, social class, gender, and geography. Identity was something you were born into more than something you created. Today, identity has become fluid and self-constructed. This offers liberating possibilities—people can explore different aspects of themselves, escape the constraints of their actual circumstances, and experiment with who they want to be. However, this fluidity also creates psychological challenges. When identity becomes something you perform and curate, the question of who you really are becomes difficult to answer. Some people become so invested in their online personas that they lose touch with their authentic self, or they experience constant anxiety about maintaining the carefully constructed image they have created. The line between authentic and performed self becomes blurred.',
    body_style
))

# Self-Worth and Online Presence
elements.append(Paragraph(
    'In some cases, people begin to measure their self-worth based on their online presence. This can be dangerous, especially for young people, who are still developing their sense of self. The need to appear perfect can lead to stress and dissatisfaction. Teenagers report anxiety when they post content and it does not receive the expected engagement. Young adults report feeling depressed when they compare their actual lives to the carefully curated versions they see online. Adults worry about their professional reputation based on their social media presence. For people whose sense of self is still forming—particularly adolescents—the impact can be profound and potentially damaging. During adolescence, one of the primary developmental tasks is forming a stable sense of identity. Normally, this process involves trying different roles, getting feedback from peers and authority figures, and gradually integrating these experiences into a coherent sense of self. But when identity formation happens in a digital environment where every action is quantified and judged, where peer feedback is immediate and public, and where the comparison group includes thousands or millions of people, the process becomes distorted. A teenager might internalize the message that their worth depends on their ability to present an attractive image online. They might engage in risky behavior or self-harm to create engaging content. They might develop eating disorders as they try to achieve the body image they see online. The long-term psychological consequences of growing up in this environment are still being researched, but early evidence suggests concerning trends in anxiety, depression, and self-harm among young people, correlating with increased social media use.',
    body_style
))

# Positive Opportunities
elements.append(Paragraph(
    'Despite all these similarities, it is important to recognize that there are also differences. Today, we have more opportunities for learning, expression, and connection. Social media can be used for education, awareness, and creativity. People can share ideas, support causes, and connect with others across cultures. A person with limited resources and modest circumstances can access world-class educational content for free through platforms like Khan Academy or YouTube. Artists can build careers without needing to be discovered by traditional gatekeepers; they can build audiences directly through social media. Activists can organize social movements with unprecedented speed and scale. Scientists collaborate across continents in real time, sharing research and solving problems together. Marginalized communities can find support, solidarity, and shared identity online in ways that would have been impossible in previous eras. People with rare diseases or unique challenges can find others with the same condition and share strategies for coping. Social media has enabled global movements for environmental awareness, gender equality, racial justice, and human rights. Educational content reaches people in developing countries who would never have had access to such information before. People separated by geography can maintain relationships that would have been impossible to sustain in the pre-digital era. The tools themselves are neutral; what matters is how we use them. The same platform that can spread misinformation can also spread life-changing knowledge.',
    body_style
))

# The Problem Is Usage
elements.append(Paragraph(
    'So, the problem is not technology itself, but how we use it. If we use technology wisely, we can continue the positive legacy of the Enlightenment—spreading knowledge, encouraging critical thinking, and improving society. But if we focus only on image, validation, and superficial success, then we are simply repeating the same patterns as the past. We have choices. We can use technology to broadcast only our achievements and hide our struggles, or we can use it to tell more honest stories about the human experience, including failure and vulnerability. We can passively absorb misinformation and accept whatever algorithms show us, or we can use our access to information as a tool for rigorous research and genuine understanding. We can measure our worth by external metrics—likes, followers, and engagement—or we can develop internal standards and a stronger sense of self-worth based on values, relationships, and genuine accomplishments. We can follow trends mindlessly, or we can think critically about what is true and valuable. We can retreat into echo chambers that confirm our biases and make us feel safe, or we can engage with perspectives different from our own, even when that engagement is uncomfortable. The Enlightenment was, at its core, about human potential—the capacity of individuals to reason, to improve themselves, and to contribute to a better world. We have not lost that capacity. The tools have changed, but our fundamental ability to think, to choose, and to act remains. The question is whether we will exercise it.',
    body_style
))

# Conclusion
elements.append(Paragraph(
    'In conclusion, it is clear that we are not completely different from people of the 18th century. While our world has changed in terms of technology and science, human nature remains largely the same. Our desire for recognition, our concern for image, and our need for approval continue to shape our behavior. The only difference is that these qualities now exist in a digital form. The tools have become more sophisticated, the platforms more global, and the pace more frantic, but the underlying impulses are ancient. We still seek status and still desire to be admired. We still want to belong to our community and still fear being rejected or judged. We still struggle between our individual desires and social expectations. We still grapple with questions of identity and self-worth. The Enlightenment was not just about the advancement of knowledge and the liberation of thought from the constraints of tradition. It was also a period of human folly, vanity, social competition, and the relentless pursuit of status and reputation. We inherited both aspects of that legacy. We have inherited the capacity for reason and critical thinking, the belief in progress through education and knowledge, and the faith in human potential for improvement. But we have also inherited the vanity, the competitiveness, and the obsession with how others perceive us. What makes the modern era different is not that we have transcended these human qualities or evolved beyond them. Rather, it is that we have created new and more efficient technological platforms for their expression. We have simply given ourselves better tools to perform the same old human dramas.',
    body_style
))

# Final Thoughts
elements.append(Paragraph(
    'Therefore, it can be said that we are still "stuck in the 18th century"—not because we have failed to progress, but because we carry the same human tendencies into every new era. The challenge for us is not just to advance technologically, but to grow intellectually and emotionally as well. We need to recognize these patterns within ourselves and make conscious choices about how we use technology and how we present ourselves to the world. We need wisdom to match our access to information. We need to cultivate self-awareness about our motivations for sharing, comparing, and validating ourselves through digital platforms. We need to remember that the Enlightenment was ultimately about the potential for human improvement—not just material or technological improvement, but moral and intellectual improvement. We have the same potential that our ancestors had, but we have better tools. The question is whether we will use these tools to improve ourselves and our world, or whether we will simply become more efficient at repeating the same mistakes. The 21st century is still young, and the story of who we become is still being written. We are not helpless prisoners of technology or human nature. We have agency. We have the ability to choose. Every day, we face decisions about how to use technology, what to share, and how to present ourselves. These small choices, multiplied across billions of people, will determine whether the digital age becomes a new enlightenment or simply a more sophisticated repetition of old patterns. The Enlightenment thinkers believed in human potential and the power of reason. That belief is as valid today as it was in the 18th century. The question is whether we have the wisdom and courage to act on it.',
    body_style
))

elements.append(Spacer(1, 1*inch))
elements.append(Paragraph('Koli Akter | ID: 056 | ENG 3102 - 18th Century English Literature', info_style))

doc.build(elements)
print('PDF created successfully: Stuck_in_18th_Century.pdf')
