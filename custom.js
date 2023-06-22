jQuery(document).ready(function($) {
    $(".featured-article .apbTitle, .featured-article .apbMeta, .featured-article .apbExcerpt, .featured-article .apbReadMore").wrapAll('<div class="featured-text"></div>');

    $(".featured-article.wp-block-ap-block-posts .apbPost .apbText .featured-text .apbMeta, .featured-article.wp-block-ap-block-posts .apbPost .apbText .featured-text .apbReadMore").wrapAll('<div class="button-box"></div>');

  var articles = $('.blog-tabs .apbGridPosts').find('article');

  // Iterate over each article and assign a class based on its number
  articles.each(function(index) {
    // Add 1 to the index since it starts from 0
    var articleNumber = index + 1;

    // Generate the class name based on the article number
    var className = numberToWord(articleNumber);

    // Add the class to the article
    $(this).addClass(className);
  });
  

});



function numberToWord(number) {
    var words = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty', 'twenty-one', 'twenty-two', 'twenty-three', 'twenty-four', 'twenty-five', 'twenty-six', 'twenty-seven', 'twenty-eight', 'twenty-nine', 'thirty', 'thirty-one', 'thirty-two', 'thirty-three', 'thirty-four', 'thirty-five', 'thirty-six', 'thirty-seven', 'thirty-eight', 'thirty-nine', 'forty', 'forty-one', 'forty-two', 'forty-three', 'forty-four', 'forty-five', 'forty-six', 'forty-seven', 'forty-eight', 'forty-nine', 'fifty', 'fifty-one', 'fifty-two', 'fifty-three', 'fifty-four', 'fifty-five', 'fifty-six', 'fifty-seven', 'fifty-eight', 'fifty-nine', 'sixty', 'sixty-one', 'sixty-two', 'sixty-three', 'sixty-four', 'sixty-five', 'sixty-six', 'sixty-seven', 'sixty-eight', 'sixty-nine', 'seventy', 'seventy-one', 'seventy-two', 'seventy-three', 'seventy-four', 'seventy-five', 'seventy-six', 'seventy-seven', 'seventy-eight', 'seventy-nine', 'eighty', 'eighty-one', 'eighty-two', 'eighty-three', 'eighty-four', 'eighty-five', 'eighty-six', 'eighty-seven', 'eighty-eight', 'eighty-nine', 'ninety', 'ninety-one', 'ninety-two', 'ninety-three', 'ninety-four', 'ninety-five', 'ninety-six', 'ninety-seven', 'ninety-eight', 'ninety-nine', 'one hundred'];
  
    if (number < words.length) {
      return words[number];
    } else {
      return '';
    }
  }